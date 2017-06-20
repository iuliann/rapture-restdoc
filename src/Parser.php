<?php

namespace Rapture\Restdoc;

/**
 * REST documentation generator
 *
 * @package Rapture\Restdoc
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Parser
{
    /**
     * Get files from source
     *
     * @param string $path Path to source
     *
     * @throws \Exception
     * @return array
     */
    public function getData(string $path, array $generic = []):array
    {
        $data = [];
        /** @var \SplFileInfo $filename */
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $filename) {
            if ($filename->isFile() && $filename->getExtension() == 'json') {
                $endpoint = json_decode(file_get_contents((string)$filename), true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new \Exception(json_last_error_msg() . ': ' . (string)$filename);
                }

                $endpoint += ['query' => [], 'params' => [], 'headers' => [], 'examples' => []];

                $endpoint = array_replace_recursive($generic, $endpoint);

                $endpoint['headers'] = $this->sanitizeParams($endpoint['headers']);
                $endpoint['params']  = $this->sanitizeParams($endpoint['params']);
                $endpoint['query']   = $this->sanitizeParams($endpoint['query']);

                ksort($endpoint['responses']);

                if (isset($endpoint['group'])) {
                    $data[$endpoint['group']][] = $endpoint;
                }
                else {
                    $data['Generic'][] = $endpoint;
                }
            }
        }

        return $data;
    }

    /**
     * @param array $params Source data
     *
     * @return array Sanitized
     */
    public function sanitizeParams(array $params)
    {
        foreach ($params as $name => $data) {
            $params[$name] += [
                'type'      =>  'int',
                'format'    =>  'int64',
                'required'  =>  true,
                'default'   =>  null,
                'description'=> ''
            ];
        }

        return $params;
    }
}

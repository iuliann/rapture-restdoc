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
     * @param string $path    Path to source
     * @param array  $generic Generic config to use with all endpoints
     *
     * @return array
     * @throws \Exception
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

                $endpoint['request'] += ['query' => [], 'params' => [], 'headers' => [], 'body' => []];

                $endpoint = array_replace_recursive($generic, $endpoint);

                $endpoint['request']['headers'] = $this->sanitizeParams($endpoint['request']['headers']);
                $endpoint['request']['params']  = $this->sanitizeParams($endpoint['request']['params']);
                $endpoint['request']['query']   = $this->sanitizeParams($endpoint['request']['query']);
                $endpoint['request']['body']    = $this->sanitizeParams($endpoint['request']['body']);

                ksort($endpoint['response']);

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

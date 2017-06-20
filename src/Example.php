<?php

namespace Rapture\Restdoc;

/**
 * REST example documentation generator
 *
 * @package Rapture\Restdoc
 * @author  Iulian N. <rapture@iuliann.ro>
 * @license LICENSE MIT
 */
class Example
{
    /**
     * Generate cURL example code
     *
     * @param array  $endpoint Endpoint array
     * @param string $url      Url to request to
     *
     * @return string
     */
    public static function curl(array $endpoint, string $url = 'http://domain.com')
    {
        if (!isset($endpoint['examples'][0])) {
            return '';
        }

        $example = $endpoint['examples'][0]['request'] + ['headers' => [], 'params' => [], 'query' => []];

        $command = $endpoint['method'] != 'GET'
            ? ["curl -X {$endpoint['method']}"]
            : ['curl'];

        $command[] = $example['query']
            ? $url . $endpoint['url'] . '?' . http_build_query($example['query'])
            : $url . $endpoint['url'];

        $command[] = "-H 'cache-control: no-cache'";
        $command[] = "-H 'content-type: application/json'";

        foreach ($example['headers'] as $name => $value) {
            $command[] = "-H '{$name}: {$value}'";
        }

        if (in_array($endpoint['method'], ['PUT', 'POST'])) {
            $command[] = "-d '" . json_encode($example['params'], JSON_PRETTY_PRINT) . "'";
        }

        return implode(" \\\n", $command);
    }

    /**
     * Generate PHP example code
     *
     * @param array  $endpoint Endpoint array
     * @param string $url      Url to request to
     *
     * @return string
     */
    public static function php(array $endpoint, string $url = 'http://example.com')
    {
        return '// @todo';
    }

    /**
     * Generate Go example code
     *
     * @param array  $endpoint Endpoint array
     * @param string $url      Url to request to
     *
     * @return string
     */
    public static function go(array $endpoint, string $url = 'http://example.com')
    {
        return '# @todo';
    }
}

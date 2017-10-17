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
        if (!isset($endpoint['example'][0])) {
            return '';
        }

        $example = $endpoint['example'][0]['request'] + ['headers' => [], 'params' => [], 'query' => [], 'body' => [], 'url' => ''];

        $command = $endpoint['method'] != 'GET'
            ? ["curl -X {$endpoint['method']}"]
            : ['curl'];

        if (count($example['params'])) {
            foreach ($example['params'] as $name => $value) {
                $endpoint['url'] = str_replace('{' . $name . '}', $value, $endpoint['url']);
            }
        }

        $command[] = $example['query']
            ? $url . ($example['url'] ?: $endpoint['url'] . '?' . http_build_query($example['query']))
            : $url . ($example['url'] ?: $endpoint['url']);

        $command[] = "-H 'cache-control: no-cache'";
        $command[] = "-H 'content-type: application/json'";

        foreach ($example['headers'] as $name => $value) {
            $command[] = "-H '{$name}: {$value}'";
        }

        if (in_array($endpoint['method'], ['PUT', 'POST'])) {
            $command[] = "-d '" . json_encode($example['body'], JSON_PRETTY_PRINT) . "'";
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

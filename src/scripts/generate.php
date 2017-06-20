<?php

require __DIR__ . '/../Parser.php';
require __DIR__ . '/../Example.php';

$srcPath = $argv[1];
$dstPath = $argv[2];
$cfgPath = $argv[3] ?? __DIR__ . '/config.json';

$restConfig    = json_decode(file_get_contents($cfgPath), true);
$restEndpoints = (new \Rapture\Restdoc\Parser())->getData($srcPath, $restConfig['default']);
$restGroups    = array_keys($restEndpoints);

ob_start();
include __DIR__ . '/../templates/' . $restConfig['template'] . '.php';
$html = ob_get_contents();
ob_end_clean();

file_put_contents($dstPath, $html);

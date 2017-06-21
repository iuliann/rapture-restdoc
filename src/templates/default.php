<?php
/** @var array $restEndpoints */
/** @var array $restGroups */
/** @var string $project */

$tableRender = function ($params)
{
    $html = '
<table class="table table-sm table-striped table-hover table-responsive table-inverse">
    <thead>
        <tr>
            <th>Name</th>
            <th>Format</th>
            <th>Required</th>
            <th>Default</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>%s</tbody>
</table>';

    $body = '';

    foreach ($params as $name => $data) {
        $data['required'] = $data['required'] ? 'yes' : 'no';
        $data['default']  = $data['default'] ?? null;
        $body .= "<tr>
                    <td><code>{$name}</code></td>
                    <td>{$data['format']}</td>
                    <td>{$data['required']}</td>
                    <td>{$data['default']}</td>
                    <td>{$data['description']}</td>
                </tr>";
    }

    return sprintf($html, $body);
};

$filterParams = function ($params, $showAll = false)
{
    foreach ($params as $name => $data) {
        $params[$name] = array_filter($data, function ($value) {
            return isset($value[0]);
        });

        if ($showAll) {
            $params[$name] += [
                'format'        =>  null,
                'default'       =>  null,
                'description'   =>  null,
                'example'       =>  null
            ];
        }
    }

    return $params;
};

$restMethods   = [
    'GET'   =>  ['alert-info',      'btn-primary'],
    'POST'  =>  ['alert-success',   'btn-success'],
    'PUT'   =>  ['alert-warning',   'btn-warning'],
    'DELETE'=>  ['alert-danger',    'btn-danger']
];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/railscasts.min.css">
    <style><?= file_get_contents(__DIR__ . '/default.css') ?></style>
</head>
<body>

<nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
    <button class="navbar-toggler navbar-toggler-right hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <a class="navbar-brand" href="#"><?= $restConfig['project'] ?></a>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <?php foreach ($restGroups as $group): ?>
                <li class="nav-item">
                    <a class="nav-link" href="#<?= $group ?>"><?= $group ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <?php foreach ($restGroups as $group): ?>

                <h1 id="<?= $group ?>"><?= $group ?></h1>

                <?php foreach ($restEndpoints[$group] as $endpoint): ?>
                    <div class="endpoint">
                        <div class="alert <?= $restMethods[$endpoint['method']][0] ?>">
                            <button class="btn <?= $restMethods[$endpoint['method']][1] ?>">
                                <?= $endpoint['method'] ?>
                            </button>
                            <strong><?= $endpoint['url'] ?></strong>
                            <span class="text-muted"><?= $endpoint['summary'] ?></span>
                        </div>

                        <div class="explain hide">
                            <div class="row">
                                <!-- Description -->
                                <div class="col-md-12 col-sm-12">
                                    <img src="https://img.shields.io/badge/version-1.0-blue.svg?style=flat-square" />

                                    <h3>Description</h3>

                                    <?= $endpoint['description'] ?>
                                </div>

                                <!-- Request -->
                                <div class="col-md-6 col-sm-12">
                                    <h3>Request</h3>
                                    <?php if (count($endpoint['request']['headers'])): ?>
                                        <div>
                                            <h4>Headers</h4>
                                            <?php if ($restConfig['showTables']): ?>
                                                <?= $tableRender($endpoint['request']['headers']) ?>
                                            <?php else: ?>
                                                <pre><code class="json"><?= json_encode($filterParams($endpoint['request']['headers'], $restConfig['showAllAttributes']), JSON_PRETTY_PRINT) ?></code></pre>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (count($endpoint['request']['params'])): ?>
                                        <div>
                                            <h4>Parameters</h4>
                                            <?php if ($restConfig['showTables']): ?>
                                                <?= $tableRender($endpoint['request']['params']) ?>
                                            <?php else: ?>
                                                <pre><code class="json"><?= json_encode($filterParams($endpoint['request']['params'], $restConfig['showAllAttributes']), JSON_PRETTY_PRINT) ?></code></pre>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (count($endpoint['request']['body'])): ?>
                                        <div>
                                            <h4>Body</h4>
                                            <?php if ($restConfig['showTables']): ?>
                                                <?= $tableRender($endpoint['request']['body']) ?>
                                            <?php else: ?>
                                                <pre><code class="json"><?= json_encode($filterParams($endpoint['request']['body'], $restConfig['showAllAttributes']), JSON_PRETTY_PRINT) ?></code></pre>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (count($endpoint['request']['query'])): ?>
                                        <div>
                                            <h4>Query</h4>
                                            <?php if ($restConfig['showTables']): ?>
                                                <?= $tableRender($endpoint['request']['query']) ?>
                                            <?php else: ?>
                                                <pre><code class="json"><?= json_encode($filterParams($endpoint['request']['query'], $restConfig['showAllAttributes']), JSON_PRETTY_PRINT) ?></code></pre>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Response -->
                                <div class="col-md-6 col-sm-12">
                                    <h3>Response</h3>
                                    <table class="table table-responsive table-sm">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Response</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($endpoint['response'] as $response): ?>
                                            <tr>
                                                <th><?= $response['code'] ?></th>
                                                <td>
                                                    <pre><code class="json"><?= json_encode($response['json'], JSON_PRETTY_PRINT) ?></code></pre>
                                                </td>
                                                <td>
                                                    <?= $response['description'] ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if (isset($endpoint['example'][0])): ?>
                                    <!-- Examples -->
                                    <div class="col-md-6 col-sm-12">
                                        <div>
                                            <ul class="nav nav-tabs" role="tablist">
                                                <?php foreach ($restConfig['examples'] as $index => $lang): ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link <?= $index == 0 ? 'active' : '' ?>"
                                                           data-toggle="tab"
                                                           href="#<?= 'tab-' . sha1($endpoint['url'] . $lang) ?>"
                                                           role="tab"><?= $lang ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <?php foreach ($restConfig['examples'] as $index => $lang): ?>
                                                    <div class="tab-pane <?= $index == 0 ? 'active' : '' ?>"
                                                         id="<?= 'tab-' . sha1($endpoint['url'] . $lang) ?>"
                                                         role="tabpanel">
                                                        <pre><code class="<?= $lang == 'cURL' ? 'bash' : strtolower($lang) ?>"><?= \Rapture\Restdoc\Example::$lang($endpoint, $restConfig['baseUrl']) ?></code></pre>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active"
                                                   data-toggle="tab"
                                                   href="#<?= 'tab-' . sha1($endpoint['url'] . 'example') ?>"
                                                   role="tab"><?= $endpoint['example'][0]['response']['code'] ?></a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            <div class="tab-pane active"
                                                 id="<?= 'tab-' . sha1($endpoint['url'] . 'example') ?>"
                                                 role="tabpanel">
                                                <pre><code class="json"><?= json_encode($endpoint['example'][0]['response']['json'], JSON_PRETTY_PRINT) ?></code></pre>
                                            </div>
                                        </div>

                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- jQuery first, then Tether, then Bootstrap JS. -->
<script src="http://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
<script type="application/javascript"><?= file_get_contents(__DIR__ . '/default.js') ?></script>
</body>
</html>

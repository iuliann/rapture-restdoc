<?php
/** @var array $restEndpoints */
/** @var array $restGroups */
/** @var string $project */

$tableRender = function ($params)
{
    $html = '
<table class="table table-striped table-hover table-responsive">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Format</th>
            <th>Required</th>
            <th>Default</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        %s
    </tbody>
</table>';

    $body = '';

    foreach ($params as $name => $data) {
        $data['required'] = $data['required'] ? 'yes' : 'no';
        $body .= "<tr>
                    <td>{$name}</td>
                    <td>{$data['type']}</td>
                    <td>{$data['format']}</td>
                    <td>{$data['required']}</td>
                    <td>{$data['default']}</td>
                    <td>{$data['description']}</td>
                </tr>";
    }

    return sprintf($html, $body);
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
            <li class="nav-item">
                <a class="nav-link" href="https://github.com/iuliann/rapture-restdoc">Help</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-sm-3 col-md-2 hidden-xs-down bg-faded sidebar">
            <ul class="nav nav-pills flex-column">

                <?php foreach ($restGroups as $group): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#<?= $group ?>"><?= $group ?></a>
                    </li>
                <?php endforeach; ?>

            </ul>
        </nav>

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">

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
                                <div class="col-md-12">
                                    <p><?= $endpoint['description'] ?></p>
                                </div>
                                <div class="col-md-8 col-sm-12">
                                    <?php if (count($endpoint['headers'])): ?>
                                        <div>
                                            <h4>Headers</h4>
                                            <?= $tableRender($endpoint['headers']) ?>
                                        </div>
                                    <?php endif; ?>


                                    <?php if (count($endpoint['params'])): ?>
                                        <div>
                                            <h4>Params</h4>
                                            <?= $tableRender($endpoint['params']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (count($endpoint['query'])): ?>
                                        <div>
                                            <h4>Query</h4>
                                            <?= $tableRender($endpoint['query']) ?>
                                        </div>
                                    <?php endif; ?>


                                    <div>
                                        <h4>Responses</h4>

                                        <table class="table table-responsive">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Response</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($endpoint['responses'] as $httpCode => $data): ?>
                                                <tr>
                                                    <th><?= $httpCode ?></th>
                                                    <td>
                                                        <pre><code class="json"><?= json_encode($data['response'], JSON_PRETTY_PRINT) ?></code></pre>
                                                    </td>
                                                    <td>
                                                        <?= $data['description'] ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <?php if (isset($endpoint['examples'][0])): ?>
                                        <div>
                                            <h5>Example #1</h5>

                                            <ul class="nav nav-tabs" role="tablist">
                                                <?php foreach ($restConfig['examples'] as $index => $lang): ?>
                                                    <li class="nav-item">
                                                        <a class="nav-link <?= $index == 0 ? 'active' : '' ?>"
                                                           data-toggle="tab"
                                                           href="#<?= 't-' . sha1($endpoint['url'] . $lang) ?>"
                                                           role="tab"><?= $lang ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <?php foreach ($restConfig['examples'] as $index => $lang): ?>
                                                    <div class="tab-pane <?= $index == 0 ? 'active' : '' ?>"
                                                         id="<?= 't-' . sha1($endpoint['url'] . $lang) ?>"
                                                         role="tabpanel">
                                                        <pre><code class="<?= $lang == 'cURL' ? 'bash' : strtolower($lang) ?>"><?= \Rapture\Restdoc\Example::$lang($endpoint, $restConfig['baseUrl']) ?></code></pre>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <div>
                                                <strong>HTTP: <?= $endpoint['examples'][0]['response']['code'] ?></strong>
                                                <pre><code class="json"><?= json_encode($endpoint['examples'][0]['response']['json'], JSON_PRETTY_PRINT) ?></code></pre>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endforeach; ?>
        </main>
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

<?php

require_once __DIR__.'/vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app['repository'] = new PrettyGit\GitRepository(__DIR__ . '/' . $config['repositoryPath']);

$app->get('/', function() use($app) {
    return $app['twig']->render(
        'index.html',
        array(
            'repositoryName' => $app['repository']->getName(),
            'currentBranch' => $app['repository']->getCurrentBranch(),
            'commits' => $app['repository']->getNumberOfCommits(),
        )
    );
});

$app->get('/stats', function() use($app) {
    return $app->json($app['repository']->getStatisticsForIndex());
});

$app['debug'] = true;
$app->run();

<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->before(function() use ($app) {
    if ($app['request']->getRequestUri() == '/error') {
        return true;
    }

    $error = false;
    $configFilePath = __DIR__ . '/config.php';
    if (!file_exists($configFilePath)) {
        return displayError('Config (config.php) file does not exists');
    }

    $config = require_once __DIR__ . '/config.php';
    if (!isset($config['repositoryPath'])) {
        return displayError('Config value for repositoryPath does not exist');
    }

    try {
        $app['repository'] = new PrettyGit\GitRepository(__DIR__ . '/' . $config['repositoryPath']);
    } catch (Exception $e) {
        return displayError('The repository path does not contain a valid git repository');
    }
});

function displayError($message) {
    global $app;
    return new Response($app['twig']->render('error.html', array('message' => $message)), 500);
}

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

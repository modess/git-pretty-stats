<?php
ini_set('memory_limit', '512M');
set_time_limit(300);
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
    $repositoryPath = 'repository';
    $configFilePath = __DIR__ . '/config.php';
    if (file_exists($configFilePath)) {
        $config = require_once __DIR__ . '/config.php';
        if (isset($config['repositoryPath'])) {
            $repositoryPath = $config['repositoryPath'];
        }
    }

    try {
        $gitWrapper = new \PHPGit_Repository(__DIR__ . '/' . $repositoryPath);
        $repository = new PrettyGit\GitRepository($gitWrapper);
        $repository->loadCommits();
        $app['repository'] = $repository;
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
            'currentBranch' => $app['repository']->getGitWrapper()->getCurrentBranch(),
            'commits' => $app['repository']->getNumberOfCommits(),
        )
    );
});

$app->get('/stats', function() use($app) {
    return $app->json(
        $app['repository']->getStatisticsForIndex()
    );
});

$app['debug'] = true;
$app->run();

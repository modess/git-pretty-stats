<?php
ini_set('memory_limit', '512M');
set_time_limit(300);
require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use GitPrettyStats\Repository;
use GitPrettyStats\RepositoryFactory;

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->error(function(\Exception $e) use($app) {
    return $app["twig"]->render("error.html", array("message" => $e->getMessage()));
});

$app->before(function() use ($app) {
    $repositoriesPath = 'repositories';

    $configFilePath = __DIR__ . '/config.php';
    if (file_exists($configFilePath)) {
        $configFile = require_once __DIR__ . '/config.php';
        if (isset($configFile['repositoriesPath'])) {
            $repositoriesPath = $configFile['repositoriesPath'];
        }
    }

    $config['repositoriesPath'] = $repositoriesPath;

    $app['config'] = $config;

    $repositoryFactory        = new RepositoryFactory($repositoriesPath);
    $app['repositories']      = $repositoryFactory->all();
    $app['repositoryFactory'] = new RepositoryFactory;

    if (count($app['repositories']) == 0) {
        throw new RuntimeException("No repositories found in path: $repositoriesPath", 0);
    }
});


function loadRepository ($app, $path) {
    if (isset($app['config']['repositoriesPath'][$path])) {
        $repositoryPath = $app['config']['repositoriesPath'][$path];
    } elseif (!realpath($path)) {
        $repositoryPath = $app['config']['repositoriesPath'] . '/' . $path;
    }

    if (!$repository = $app['repositoryFactory']->loadRepository($repositoryPath)) {
        throw new RuntimeException('The repository path does not contain a valid git repository', 0, $e);
    }

    try {
        $repository->loadCommits();
    } catch (Exception $e) {
        // Catch all possible errors while loading the repository, re-wrap it with a friendlier
        // message and re-throw so it's caught by the error handler:
        // (the original exception is chained to the new one):
        throw new RuntimeException('Could not load commits from repository', 0, $e);
    }

    return $repository;
}

$app->get('/', function () use ($app) {
    return $app['twig']->render(
        'index.html',
        array(
            'repositories' => $app['repositories']
        )
    );
});

$app->get('repository/{path}', function ($path) use ($app) {
    $repository = loadRepository($app, $path);

    return $app['twig']->render(
        'repository.html',
        array(
            'repositories'  => $app['repositories'],
            'name'          => $repository->getName(),
            'branch'        => $repository->gitter->getCurrentBranch(),
            'statsEndpoint' => $app["request"]->getBaseUrl() . "/git-stats/" . $path,
        )
    );
});

$app->get('/git-stats/{path}', function($path) use($app) {
    $repository = loadRepository($app, $path);

    return $app->json(
        $repository->getStatistics()
    );
});

$app['debug'] = true;
$app->run();

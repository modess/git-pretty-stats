<?php
ini_set('memory_limit', '512M');
set_time_limit(300);
require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use GitPrettyStats\Repository;
use GitPrettyStats\RepositoryFactory;

$app = new Silex\Application();

//
// Register view service provider
//
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

//
// Register error handler
//
$app->error(function(\Exception $e) use($app) {
    return $app["twig"]->render("error.html", array("message" => $e->getMessage()));
});

//
// Register hook that runs before the application
//
$app->before(function() use ($app) {
    $repositoriesPath = 'repositories';

    $configFilePath = __DIR__ . '/config.php';
    $config = (file_exists($configFilePath)) ? require_once __DIR__ . '/config.php' : null;

    $app['repositoryFactory'] = new RepositoryFactory($config);
    $app['repositories']      = $app['repositoryFactory']->all();

    if (count($app['repositories']) == 0) {
        throw new RuntimeException("No repositories found in path: $repositoriesPath", 0);
    }
});

//
// Default route
//
$app->get('/', function () use ($app) {
    return $app['twig']->render(
        'index.html',
        array(
            'repositories' => $app['repositoryFactory']->toArray()
        )
    );
});

//
// Repository route
//
$app->get('repository/{name}', function ($name) use ($app) {
    $repository = $app['repositoryFactory']->fromName($name);

    return $app['twig']->render(
        'repository.html',
        array(
            'repositories'  => $app['repositories'],
            'name'          => $repository->getName(),
            'branch'        => $repository->gitter->getCurrentBranch(),
            'statsEndpoint' => $app["request"]->getBaseUrl() . "/git-stats/" . $name,
        )
    );
});

//
// Repository statistics route
//
$app->get('/git-stats/{name}', function($name) use($app) {
    $repository = $app['repositoryFactory']->fromName($name);
    $repository->loadCommits();

    return $app->json(
        $repository->getStatistics()
    );
});

$app['debug'] = true;
$app->run();

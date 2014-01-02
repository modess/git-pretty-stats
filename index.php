<?php
ini_set('memory_limit', '512M');
set_time_limit(300);
require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use GitPrettyStats\Repository;
use GitPrettyStats\RepositoryFactory;

$app = new Silex\Application();

//
// Register configuration service provider
//
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . "/config.php"));

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
    $app['config'] = null;

    try {
        $app['config'] = $app['repositoriesPath'];
    } catch (InvalidArgumentException $e) {
        // Config file not setup
    }

    $app['repositoryFactory'] = new RepositoryFactory($app['config']);
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
    $cache_file = __DIR__.'/cache/'.$name.'.json';
    $from_cache
        = isset($app['config']['cacheTime'])
        && $app['config']['cacheTime']
        && file_exists($cache_file)
        && filemtime($cache_file) > (time() - $app['config']['cacheTime']);
    if ($from_cache) {
        $statistics = json_decode(file_get_contents($cache_file));
    } else {
        $repository = $app['repositoryFactory']->fromName($name);
        $repository->loadCommits();
        $statistics = $repository->getStatistics();
        file_put_contents($cache_file, json_encode($statistics));
    }
    return $app->json(
        $statistics
    );
});

$app->run();

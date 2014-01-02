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
    $app['config'] = (file_exists($configFilePath)) ? require_once __DIR__ . '/config.php' : null;

    $app['repositoryFactory'] = new RepositoryFactory($app['config']);
    $app['repositories']      = $app['repositoryFactory']->all();

    if (count($app['repositories']) == 0) {
        throw new RuntimeException("No repositories found in path: $repositoriesPath", 0);
    }
});

$app->get('/', function () use ($app) {
    return $app['twig']->render(
        'index.html',
        array(
            'repositories' => $app['repositoryFactory']->toArray()
        )
    );
});

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

$app['debug'] = true;
$app->run();

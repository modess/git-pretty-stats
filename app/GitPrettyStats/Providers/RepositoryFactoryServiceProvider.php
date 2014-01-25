<?php
namespace GitPrettyStats\Providers;

use Illuminate\Support\ServiceProvider;
use GitPrettyStats\RepositoryFactory;

/**
 * Service provider for repository factory
 * @codeCoverageIgnore
 */
class RepositoryFactoryServiceProvider extends ServiceProvider
{
    public function register ()
    {
        $this->app->bind('RepositoryFactory', function () {
            return new RepositoryFactory;
        });
    }
}

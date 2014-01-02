<?php
namespace GitPrettyStats\Providers;

use Illuminate\Support\ServiceProvider;
use GitPrettyStats\RepositoryFactory;

class RepositoryFactoryServiceProvider extends ServiceProvider
{
    public function register ()
    {
        $this->app->bind('RepositoryFactory', function () {
            return new RepositoryFactory;
        });
    }
}

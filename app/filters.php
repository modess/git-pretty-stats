<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
 */
use Symfony\Component\Process\ExecutableFinder;

App::before(function($request)
{
    if (!ini_get('date.timezone')) {
        return View::make('error.datetimezone');
    }

    $finder = new ExecutableFinder;
    $gitExecutable = $finder->find('git', '/usr/bin/git');
    if (!is_executable($gitExecutable)) {
        return View::make('error.git');
    }
});

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

App::before(function($request)
{
    if (!ini_get('date.timezone')) {
        return View::make('error.datetimezone');
    }
});


App::after(function($request, $response)
{
    //
});


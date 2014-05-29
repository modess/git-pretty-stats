<?php

Route::get('/', 'MainController@index');

Route::get('/repository', 'RepositoryController@all');
Route::get('/repository/{name}', 'RepositoryController@data');

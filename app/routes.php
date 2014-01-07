<?php

Route::get('/', 'RepositoryController@index');
Route::get('/repository', 'RepositoryController@all');
Route::get('/repository/{name}', 'RepositoryController@show');

<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/',
    'as'         => 'map::',
    'namespace'  => 'Modules\Map\Http\Controllers',
], function () {
    Route::resource('maps', 'MapController');
});

Route::group([
    'middleware' => 'web',
    'prefix'     => 'api/maps/',
    'as'         => 'map::',
    'namespace'  => 'Modules\Map\Http\Controllers',
], function () {
    Route::get('{map}/get-markers', [
        'as'   => 'api.get-markers',
        'uses' => 'ApiController@getMarkers',
    ]);
});

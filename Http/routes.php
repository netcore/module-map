<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/',
    'as'         => 'map::',
    'namespace'  => 'Modules\Map\Http\Controllers',
], function () {

    Route::resource('maps', 'MapController');

});

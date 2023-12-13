<?php

Route::group(
    [
        'middleware' => ['web'],
        'prefix' => config('ipsum.admin.route_prefix').'/media',
        'namespace' => '\Ipsum\Media\app\Http\Controllers'
    ],
    function() {


        Route::get("", array(
            "as" => "admin.media.index",
            "uses" => "MediaController@index",
        ));
        Route::get("popin", array(
            "as" => "admin.media.popin",
            "uses" => "MediaController@popin",
        ));
        Route::get("new", array(
            "as" => "admin.media.new",
            "uses" => "MediaController@new",
        ));
        Route::get("publication/{type?}", array(
            "as" => "admin.media.publication",
            "uses" => "MediaController@publication",
        ));
        Route::post('changeOrder/{type?}', array(
            'as'     => 'admin.media.changeOrder',
            'uses'   => 'MediaController@changeOrder'
        ));
        Route::post("", array(
            "as" => "admin.media.store",
            "uses" => "MediaController@store",
        ));
        Route::get("create", array(
            "as" => "admin.media.create",
            "uses" => "MediaController@create",
        ));
        Route::delete("{media}", array(
            "as" => "admin.media.destroy",
            "uses" => "MediaController@destroy",
        ));
        Route::get("{media}/destroy", array(
            "as" => "admin.media.getDestroy",
            "uses" => "MediaController@destroy",
        ));
        Route::put("{media}/{locale?}", array(
            "as" => "admin.media.update",
            "uses" => "MediaController@update",
        ));
        Route::get("{media}/edit/{locale?}", array(
            "as" => "admin.media.edit",
            "uses" => "MediaController@edit",
        ));
        Route::get("{media}/crop/{locale?}", array(
            "as" => "admin.media.crop",
            "uses" => "MediaController@crop",
        ));
        Route::post("{media}/crop_update/{locale?}", array(
            "as" => "admin.media.crop_update",
            "uses" => "MediaController@crop_update",
        ));

    }
);

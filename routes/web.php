<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/download-collection', function () {
    $path = base_path('WMS_Postman_Collection.json');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->download($path, 'WMS_Postman_Collection.json');
})->name('download.collection');


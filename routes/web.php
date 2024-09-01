<?php

use Illuminate\Support\Facades\Route;

Route::any('{any}', function () {
    return redirect('/admin');
})->where('any', '.*');

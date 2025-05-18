<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

route::get('/', function () {
    return redirect()->route('import.index');
});

Route::controller(ImportController::class)->group(function () {
    Route::get('import', 'index')->name('import.index');
    Route::post('import', 'create')->name('import.create');
});

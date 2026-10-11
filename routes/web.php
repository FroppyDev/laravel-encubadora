<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::view('/home', 'home')->middleware(['auth', 'verified'])->name('home');

<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/notifications', function () {
    return auth()->user()->unreadNotifications;
});

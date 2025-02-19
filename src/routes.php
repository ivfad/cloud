<?php

use App\Controllers\AdminController;
use App\Controllers\HomeController;
use App\Controllers\RegistrationController;
use App\Controllers\UserController;
use Core\Router\Route;

/**
 * Here is where web routes can be registered
 * Examples:
 * Route::get('/example1', function() { echo 'Example1'; }),
 * Route::get('/example2/{id}/{user}', [ExampleController::class, 'index'])->access('guest'),
 */

return [

    Route::get('/', [HomeController::class, 'index']),

    Route::get('/register', [RegistrationController::class, 'index'])->access('guest'),
    Route::post('/register', [RegistrationController::class, 'store'])->access('guest'),

    Route::get('/users/list', [UserController::class, 'list'])->access('user'),
    Route::get('/users/get/{id}', [UserController::class, 'get'])->access('user'),
    Route::get('/users/update', [UserController::class, 'updateView'])->access('user'),
    Route::put('/users/update', [UserController::class, 'update'])->access('user'),
    Route::get('/login', [UserController::class, 'loginView'])->access('guest'),
    Route::post('/login', [UserController::class, 'login'])->access('guest'),
    Route::get('/logout', [UserController::class, 'logout'])->access('user'),
    Route::get('/reset_password', [UserController::class, 'reset_password'])->access('user'),

    Route::get('/admin/users/list', [AdminController::class, 'list'])->access('admin'),
    Route::get('/admin/users/get/{id}', [AdminController::class, 'get'])->access('admin'),
    Route::put('/admin/users/update/{id}', [AdminController::class, 'update'])->access('admin'),
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'delete'])->access('admin'),

];

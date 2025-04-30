<?php

use App\Controllers\AdminController;
use App\Controllers\FileController;
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

    //    HomeController endpoints
    Route::get('/', [HomeController::class, 'index']),

    //    RegistrationController endpoints
    Route::get('/register', [RegistrationController::class, 'index'])->access('guest'),
    Route::post('/register', [RegistrationController::class, 'store'])->access('guest'),
    Route::get('/greeting', [RegistrationController::class, 'greeting'])->access('firstUser'),

    //    UserController endpoints
    Route::get('/users/list', [UserController::class, 'list'])->access('user'),
    Route::get('/users/get/{id}', [UserController::class, 'get'])->access('user'),
    Route::get('/users/update', [UserController::class, 'updateView'])->access('user'),
    Route::put('/users/update', [UserController::class, 'update'])->access('user'),
    Route::get('/login', [UserController::class, 'loginView'])->access('guest'),
    Route::post('/login', [UserController::class, 'login'])->access('guest'),
    Route::get('/logout', [UserController::class, 'logout'])->access('user'),
    Route::get('/reset_password', [UserController::class, 'reset_password'])->access('user'),

    //    AdminController endpoints
    Route::get('/admin/users/list', [AdminController::class, 'list'])->access('admin'),
    Route::get('/admin/users/get/{id}', [AdminController::class, 'get'])->access('admin'),
    Route::get('/admin/users/update', [AdminController::class, 'updateView'])->access('admin'),
    Route::put('/admin/users/update/{id}', [AdminController::class, 'update'])->access('admin'),
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'delete'])->access('admin'),

    //    FileController endpoints
    Route::get('/files/list', [FileController::class, 'list'])->access('user'),
    Route::get('/files/get/{id}', [FileController::class, 'get'])->access('user'),
    Route::post('/files/add', [FileController::class, 'add'])->access('user'),
    Route::put('/files/rename/{id}', [FileController::class, 'rename'])->access('user'),
    Route::delete('/files/remove/{id}', [FileController::class, 'remove'])->access('user'),
    Route::get('/directories/get/{id}', [FileController::class, 'getFolder'])->access('user'),
    Route::post('/directories/add', [FileController::class, 'addFolder'])->access('user'),
    Route::put('/directories/rename/{id}', [FileController::class, 'renameFolder'])->access('user'),
    Route::delete('/directories/delete/{id}', [FileController::class, 'removeFolder'])->access('user'),
    Route::get('/files/share/{id}', [FileController::class, 'accessList'])->access('user'),
    Route::put(' /files/share/{id}/{user_id}', [FileController::class, 'accessShare'])->access('user'),
    Route::delete(' /files/share/{id}/{user_id}', [FileController::class, 'accessDelete'])->access('user'),

];

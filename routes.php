<?php

use App\Controllers\AdminController;
use App\Controllers\UserController;
use App\Controllers\HomeController;
use App\Controllers\TestController;
use Core\Route;

/**
 * Here is where web routes can be registered
 */

return [
    Route::get('/', [HomeController::class, 'index']),

    Route::get('/users/list', [UserController::class, 'list'])->access('user'),
    Route::get('/users/get/{id}', [UserController::class, 'get'])->access('user'),
    Route::put('/users/update', [UserController::class, 'update'])->access('user'),
    Route::get('/login', [UserController::class, 'loginView'])->access('guest'),
    Route::post('/login', [UserController::class, 'login'])->access('guest'),
    Route::get('/logout', [UserController::class, 'logout'])->access('user'),

    Route::get('/reset_password', [UserController::class, 'reset_password'])->access('user'),

    Route::get('/register', [App\Controllers\RegistrationController::class, 'index'])->access('guest'),
    Route::post('/register', [App\Controllers\RegistrationController::class, 'store'])->access('guest'),

    Route::get('/jwt', [UserController::class, 'jwt'])->access('user'),

//    Route::get('/login', [UserController::class, 'loginView'])->access('guest'),
//    Route::post('/login', [UserController::class, 'login']),
//    Route::get('/logout', [UserController::class, 'logout']),

    Route::get('/admin/users/list', [AdminController::class, 'list'])->access('admin'),
    Route::get('/admin/users/get/{id}', [AdminController::class, 'get'])->access('admin'),

    Route::put('/admin/users/update/{id}', [AdminController::class, 'update'])->access('admin'),
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'delete'])->access('admin'),

//    Route::get('/users/list', [UserController::class, 'list']),
//    Route::get('/users/get/{id}', [UserController::class, 'get']),
//    Route::get('/users/get/{id}', [UserController::class, 'get'])->access('user'),

//    Route::get('/users/update', [UserController::class, 'update'])->access('user'), //test
//    Route::get('/users/update', [UserController::class, 'updateView'])->access('user'),
//    Route::put('/users/update', [UserController::class, 'update'])->access('user'),
//    Route::post('/users/update', [UserController::class, 'update'])->access('user'),
//    Route::get('/users/update', [UserController::class, 'update'])->access('user'),
//    Route::put('/users/update', [UserController::class, 'update']),
//    Route::post('/users/update', [UserController::class, 'update']),
//    Route::get('/login', [UserController::class, 'loginView'])->access('guest'), //post!!
//    Route::get('/login', [UserController::class, 'loginView']), //post!!


//    Route::get('/update', [UserController::class, 'update'])->access('user'),


//    Route::get('/logout', [UserController::class, 'logout'])->access('admin'),

//    Route::get('/test3', [TestController::class, 'index']),
//    Route::get('/files/share/{id}/{user}', [UserController::class, 'test']),



//    Route::get('/', [\App\Controllers\HomeController::class, 'index'])::access(),
//    Route::access(Route::get('/', [\App\Controllers\HomeController::class, 'index'])),

//    Route::get('/test2', [TestController::class, 'index'])->access('user'),
//    Route::put('/test2', [TestController::class, 'index']),
////    Route::post('/test3', \App\Controllers\TestController::class),
//    Route::get('/test', function() {
//        echo '123';
//    }),
];

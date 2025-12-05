<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

// Redirect root URL to /home
Route::get('/', function () {
    return redirect()->route('home');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
Route::get('/chat/new', [App\Http\Controllers\ChatController::class, 'newChat'])->name('chat.new');
Route::get('/chat/{id}', [App\Http\Controllers\ChatController::class, 'loadConversation'])->name('chat.load');

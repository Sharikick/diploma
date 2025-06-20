<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ValidationController;
use Illuminate\Support\Facades\Route;


Route::middleware("auth")->group(function () {
    Route::post("/upload", [DocumentController::class, "upload"]);
    Route::get("/dashboard", fn () => view("dashboard"))->name("dashboard");

    Route::get("/history", [DocumentController::class, 'history'])->name("history");
    Route::get('/history/{validation}', [ValidationController::class, 'show'])->name('validation');
});

Route::middleware("guest")->group(function () {
    Route::get("/welcome", fn () => view("welcome"))->name("welcome");

    Route::get("/login", [LoginController::class, 'create'])->name("login");
    Route::post("/login", [LoginController::class, 'store']);

    Route::get("/register", [RegisterController::class, 'create'])->name("register");
    Route::post("/register", [RegisterController::class, 'store']);
});

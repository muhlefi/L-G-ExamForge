<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

// ── Batches (Sesi) ──────────────────────────────────────────────
Route::get('/',                  [BatchController::class,   'index'])  ->name('batches.index');
Route::get('/batches/create',    [BatchController::class,   'create']) ->name('batches.create');
Route::post('/batches',          [BatchController::class,   'store'])  ->name('batches.store');
Route::get('/batches/{batch}',   [QuestionController::class,'show'])   ->name('batches.show');

// ── Question Groups ─────────────────────────────────────────────
Route::post('/batches/{batch}/groups',  [QuestionController::class, 'storeGroup'])    ->name('groups.store');
Route::post('/groups/{group}/generate', [QuestionController::class, 'generateGroup']) ->name('groups.generate');
Route::delete('/groups/{group}',        [QuestionController::class, 'destroyGroup'])  ->name('groups.destroy');

// ── Questions CRUD ──────────────────────────────────────────────
Route::patch('/questions/{question}',  [QuestionController::class, 'update'])  ->name('questions.update');
Route::delete('/questions/{question}', [QuestionController::class, 'destroy']) ->name('questions.destroy');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;





Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
Route::get('/contacts/filter', [ContactController::class, 'filter'])->name('contacts.filter');

Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');

Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
Route::post('/contacts/merge', [ContactController::class, 'merge'])->name('contacts.merge');



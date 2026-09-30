<?php

use App\Http\Controllers\FormServiceAdvisorController;
use App\Http\Controllers\MasterCustomerController;
use App\Http\Controllers\MasterJasaController;
use App\Http\Controllers\MasterMekanikController;
use App\Http\Controllers\MasterPartController;
use Illuminate\Support\Facades\Route;

// Redirect root to Master Jasa
Route::get('/', function () {
    return redirect()->route('master-jasa.index');
});

// Master Jasa Routes
Route::prefix('master-jasa')->name('master-jasa.')->group(function () {
    Route::get('/data', [MasterJasaController::class, 'data'])->name('data');
    Route::get('/', [MasterJasaController::class, 'index'])->name('index');
    Route::post('/', [MasterJasaController::class, 'store'])->name('store');
    Route::get('/{master_jasa}', [MasterJasaController::class, 'show'])->name('show');
    Route::put('/{master_jasa}', [MasterJasaController::class, 'update'])->name('update');
    Route::delete('/{master_jasa}', [MasterJasaController::class, 'destroy'])->name('destroy');
});

// Master Part Routes
Route::prefix('master-part')->name('master-part.')->group(function () {
    Route::get('/data', [MasterPartController::class, 'data'])->name('data');
    Route::get('/', [MasterPartController::class, 'index'])->name('index');
    Route::post('/', [MasterPartController::class, 'store'])->name('store');
    Route::get('/{master_part}', [MasterPartController::class, 'show'])->name('show');
    Route::put('/{master_part}', [MasterPartController::class, 'update'])->name('update');
    Route::delete('/{master_part}', [MasterPartController::class, 'destroy'])->name('destroy');
});

// Master Mekanik Routes
Route::prefix('master-mekanik')->name('master-mekanik.')->group(function () {
    Route::get('/data', [MasterMekanikController::class, 'data'])->name('data');
    Route::get('/', [MasterMekanikController::class, 'index'])->name('index');
    Route::post('/', [MasterMekanikController::class, 'store'])->name('store');
    Route::get('/{master_mekanik}', [MasterMekanikController::class, 'show'])->name('show');
    Route::put('/{master_mekanik}', [MasterMekanikController::class, 'update'])->name('update');
    Route::delete('/{master_mekanik}', [MasterMekanikController::class, 'destroy'])->name('destroy');
});

// Master Customer Routes
Route::prefix('master-customer')->name('master-customer.')->group(function () {
    Route::get('/data', [MasterCustomerController::class, 'data'])->name('data');
    Route::get('/', [MasterCustomerController::class, 'index'])->name('index');
    Route::post('/', [MasterCustomerController::class, 'store'])->name('store');
    Route::get('/{master_customer}', [MasterCustomerController::class, 'show'])->name('show');
    Route::put('/{master_customer}', [MasterCustomerController::class, 'update'])->name('update');
    Route::delete('/{master_customer}', [MasterCustomerController::class, 'destroy'])->name('destroy');
});

// Form Service Advisor Routes
Route::prefix('form-sa')->name('form-sa.')->group(function () {
    Route::get('/data', [FormServiceAdvisorController::class, 'data'])->name('data');
    Route::get('/', [FormServiceAdvisorController::class, 'index'])->name('index');
    Route::post('/', [FormServiceAdvisorController::class, 'store'])->name('store');
    Route::get('/{id_form_sa}', [FormServiceAdvisorController::class, 'show'])->where('id_form_sa', '.*')->name('show');
});

// Work Order Routes
use App\Http\Controllers\WorkOrderController;

Route::prefix('work-orders')->name('work-orders.')->group(function () {
    Route::get('/', [WorkOrderController::class, 'index'])->name('index');
    Route::get('/create/{id_form_sa}', [WorkOrderController::class, 'create'])->where('id_form_sa', '.*')->name('create');
    Route::post('/', [WorkOrderController::class, 'store'])->name('store');
    Route::get('/{id_wo}/edit', [WorkOrderController::class, 'edit'])->where('id_wo', '.*')->name('edit');
    
    // API Routes for Cart
    Route::post('/{id_wo}/add-part', [WorkOrderController::class, 'addPart'])->where('id_wo', '.*')->name('addPart');
    Route::delete('/remove-part/{id_detail}', [WorkOrderController::class, 'removePart'])->name('removePart');
    Route::post('/{id_wo}/add-jasa', [WorkOrderController::class, 'addJasa'])->where('id_wo', '.*')->name('addJasa');
    Route::delete('/remove-jasa/{id_detail}', [WorkOrderController::class, 'removeJasa'])->name('removeJasa');
    
    // Complete WO
    Route::post('/{id_wo}/complete', [WorkOrderController::class, 'complete'])->where('id_wo', '.*')->name('complete');
});

// Nota Routes
use App\Http\Controllers\NotaController;

Route::prefix('nota')->name('nota.')->group(function () {
    Route::get('/', [NotaController::class, 'index'])->name('index');
});

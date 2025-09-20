<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FormularioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\FrameworkController;
use App\Http\Controllers\ContentFrameworkProjectController;
use App\Http\Controllers\InvestigationLineController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ResearchGroupController;
use App\Http\Controllers\ThematicAreaController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::middleware(['auth', 'role:user'])->group(function () {
    // Rutas para obtener ciudades por departamento
    Route::get('obtener-ciudades-por-departamento/{id}', [DepartmentController::class, 'ciudadesPorDepartamento'])->name('obtener-ciudades-por-departamento');
    Route::get('/obtener-ciudades/{id_departamento}', [DepartmentController::class, 'ciudadesPorDepartamento']);
});

Route::middleware(['auth', 'role:research_staff'])->group(function () {
    // Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    
    // Departamentos
    Route::resource('/departamento', DepartmentController::class);
    Route::get('/departamentos', [DepartmentController::class, 'index'])->name('departamentos.index');
    Route::get('/obtener-ciudades-por-departamento/{id}', [DepartmentController::class, 'ciudadesPorDepartamento'])->name('obtener-ciudades-por-departamento');
    
    // Ciudades
    Route::resource('/ciudad', CityController::class);
    Route::get('obtener-ciudades-por-departamento/{id}', [CityController::class, 'obtenerCiudadesPorDepartamento']);
    
    // Formularios
    Route::resource('/formulario', FormularioController::class);

    // Estructura académica avanzada
    Route::resource('research-groups', ResearchGroupController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('investigation-lines', InvestigationLineController::class);
    Route::resource('thematic-areas', ThematicAreaController::class);
});

// Rutas públicas para departamentos y ciudades (si las necesitas sin autenticación)
Route::get('/obtener-ciudades-por-departamento/{id}', [DepartmentController::class, 'ciudadesPorDepartamento'])->name('obtener-ciudades-por-departamento');
Route::get('/obtener-ciudades/{id_departamento}', [DepartmentController::class, 'ciudadesPorDepartamento']);Route::resource('/framework', App\Http\Controllers\FrameworkController::class);

//Rutas de framework y content framework project
Route::resource('frameworks', FrameworkController::class);
Route::resource('content-framework-projects', ContentFrameworkProjectController::class);

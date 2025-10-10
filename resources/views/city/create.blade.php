{{--
    View path: city/create.blade.php.
    Purpose: Renders the create.blade view for the City module.
    This template does not rely on dynamic variables.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Nueva ciudad')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb clarifies navigation context so the user knows the parent section. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- Link points back to the index allowing the user to abort the creation flow. --}}
                            <li class="breadcrumb-item"><a href="{{ route('cities.index') }}">Ciudades</a></li>
                            {{-- Static crumb indicating the current "create" step in the hierarchy. --}}
                            <li class="breadcrumb-item active" aria-current="page">Nueva</li>
                        </ol>
                    </nav>
                    {{-- Title summarises that the following form will capture a new city. --}}
                    <h2 class="page-title">Registrar ciudad</h2>
                    {{-- Helper sentence provides additional guidance about the relationship with departments. --}}
                    <p class="text-muted mb-0">Crea una nueva ciudad y asóciala a un departamento existente.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    {{-- Section label groups the upcoming form fields by topic. --}}
                    <h3 class="card-title">Datos de la ciudad</h3>
                </div>
                <div class="card-body">
                    {{-- Form element sends the captured data to the specified endpoint. --}}
                    <form action="{{ route('cities.store') }}" method="POST" novalidate>
                        @csrf
                        {{-- Shared partial renders the reusable form inputs for both create and edit screens. --}}
                        @includeFirst(['city.form', 'cities.form'])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

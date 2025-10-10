{{--
    View path: city/edit.blade.php.
    Purpose: Renders the edit.blade view for the City module.
    Expected variables within this template: $city.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('tablar::page')

@section('title', 'Editar ciudad')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    {{-- Breadcrumb trail confirms the current item and step in the editing flow. --}}
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            {{-- First crumb returns to the general listing if the user cancels. --}}
                            <li class="breadcrumb-item"><a href="{{ route('cities.index') }}">Ciudades</a></li>
                            {{-- Second crumb links to the detail page for quick verification before editing. --}}
                            <li class="breadcrumb-item"><a href="{{ route('cities.show', $city) }}">{{ $city->name }}</a></li>
                            {{-- Final crumb indicates the current edit action. --}}
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                    {{-- Title communicates that existing information will be updated. --}}
                    <h2 class="page-title">Editar ciudad</h2>
                    {{-- Contextual helper assures the user about the scope of the update. --}}
                    <p class="text-muted mb-0">Actualiza los datos de la ciudad seleccionada.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    {{-- Heading groups the editable attributes of the city. --}}
                    <h3 class="card-title">Datos de la ciudad</h3>
                </div>
                <div class="card-body">
                    {{-- Form element sends the captured data to the specified endpoint. --}}
                    <form action="{{ route('cities.update', $city) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')
                        {{-- Partial reused from the create screen keeps the form consistent across flows. --}}
                        @includeFirst(['city.form', 'cities.form'])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

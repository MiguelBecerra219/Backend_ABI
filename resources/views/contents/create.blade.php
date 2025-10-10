{{--
    View path: contents/create.blade.php.
    Purpose: Renders the create.blade view for the Contents module.
    Expected variables within this template: $framework.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('layouts.app')

@section('content')
    {{-- Heading communicates that we are adding a new goal to the selected framework. --}}
    <h1>Crear Objetivo para {{ $framework->name }}</h1>
    {{-- Form element sends the captured data to the specified endpoint. --}}
    <form method="POST" action="{{ route('frameworks.contents.store', $framework) }}">
        @csrf
        <div>
            {{-- Label describing the purpose of 'Nombre'. --}}
            <label for="name">Nombre</label>
            {{-- Input element used to capture the 'name' value. --}}
            <input type="text" id="name" name="name" value="{{ old('name') }}">
        </div>
        <div>
            {{-- Label describing the purpose of 'Descripción'. --}}
            <label for="description">Descripción</label>
            {{-- Multiline textarea allowing a detailed description for 'description'. --}}
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>
        {{-- Action row groups navigation and submit controls for clarity. --}}
        {{-- Button element of type 'submit' to trigger the intended action. --}}
        <button type="submit">Guardar</button>
    </form>
@endsection

{{--
    View path: contents/edit.blade.php.
    Purpose: Renders the edit.blade view for the Contents module.
    Expected variables within this template: $content, $fw.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('layouts.app')

@section('content')
    {{-- Heading informs administrators they are modifying an existing learning objective. --}}
    <h1>Editar Objetivo</h1>
    {{-- Form element sends the captured data to the specified endpoint. --}}
    <form method="POST" action="{{ route('contents.update', $content) }}">
        @csrf
        @method('PUT')
        <div>
            {{-- Label describing the purpose of 'Nombre'. --}}
            <label for="name">Nombre</label>
            {{-- Input element used to capture the 'name' value. --}}
            <input type="text" id="name" name="name" value="{{ old('name', $content->name) }}">
        </div>
        <div>
            {{-- Label describing the purpose of 'Descripción'. --}}
            <label for="description">Descripción</label>
            {{-- Multiline textarea allowing a detailed description for 'description'. --}}
            <textarea id="description" name="description">{{ old('description', $content->description) }}</textarea>
        </div>
        <div>
            {{-- Label describing the purpose of 'Framework'. --}}
            <label for="framework_id">Framework</label>
            {{-- Dropdown presenting the available options for 'framework_id'. --}}
            <select id="framework_id" name="framework_id">
                @foreach(\App\Models\Framework::all() as $fw)
                    <option value="{{ $fw->id }}" {{ old('framework_id', $content->framework_id) == $fw->id ? 'selected' : '' }}>{{ $fw->name }}</option>
                @endforeach
            </select>
        </div>
        {{-- Action row groups navigation and submit controls for clarity. --}}
        {{-- Button element of type 'submit' to trigger the intended action. --}}
        <button type="submit">Actualizar</button>
    </form>
@endsection

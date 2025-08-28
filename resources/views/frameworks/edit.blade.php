@extends('layouts.app')

@section('content')
    <h1>Editar Framework</h1>
    <form method="POST" action="{{ route('frameworks.update', $framework) }}">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name', $framework->name) }}">
        </div>
        <div>
            <label for="description">Descripción</label>
            <textarea id="description" name="description">{{ old('description', $framework->description) }}</textarea>
        </div>
        <div>
            <label for="start_year">Año Inicio</label>
            <input type="number" id="start_year" name="start_year" value="{{ old('start_year', $framework->start_year) }}">
        </div>
        <div>
            <label for="end_year">Año Fin</label>
            <input type="number" id="end_year" name="end_year" value="{{ old('end_year', $framework->end_year) }}">
        </div>
        <button type="submit">Actualizar</button>
    </form>
@endsection

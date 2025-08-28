@extends('layouts.app')

@section('content')
    <h1>Crear Objetivo para {{ $framework->name }}</h1>
    <form method="POST" action="{{ route('frameworks.contents.store', $framework) }}">
        @csrf
        <div>
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
        </div>
        <div>
            <label for="description">Descripción</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>
        <button type="submit">Guardar</button>
    </form>
@endsection

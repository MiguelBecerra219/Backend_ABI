@extends('layouts.app')

@section('content')
    <h1>Editar Objetivo</h1>
    <form method="POST" action="{{ route('contents.update', $content) }}">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name', $content->name) }}">
        </div>
        <div>
            <label for="description">Descripción</label>
            <textarea id="description" name="description">{{ old('description', $content->description) }}</textarea>
        </div>
        <div>
            <label for="framework_id">Framework</label>
            <select id="framework_id" name="framework_id">
                @foreach(\App\Models\Framework::all() as $fw)
                    <option value="{{ $fw->id }}" {{ old('framework_id', $content->framework_id) == $fw->id ? 'selected' : '' }}>{{ $fw->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Actualizar</button>
    </form>
@endsection

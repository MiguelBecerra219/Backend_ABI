@extends('layouts.app')

@section('content')
    <h1>{{ $content->name }}</h1>
    <p>{{ $content->description }}</p>
    <a href="{{ route('frameworks.show', $content->framework) }}">Ver Framework</a>
    <a href="{{ route('contents.edit', $content) }}">Editar</a>
    <form method="POST" action="{{ route('contents.destroy', $content) }}" style="display:inline">
        @csrf
        @method('DELETE')
        <button type="submit">Eliminar</button>
    </form>
@endsection

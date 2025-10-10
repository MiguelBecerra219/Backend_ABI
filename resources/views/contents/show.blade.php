{{--
    View path: contents/show.blade.php.
    Purpose: Renders the show.blade view for the Contents module.
    Expected variables within this template: $content.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('layouts.app')

@section('content')
    {{-- Title reflects the goal currently being reviewed. --}}
    <h1>{{ $content->name }}</h1>
    {{-- Description summarises what the goal aims to achieve. --}}
    <p>{{ $content->description }}</p>
    {{-- Link allows navigation back to the parent framework for broader context. --}}
    <a href="{{ route('frameworks.show', $content->framework) }}">Ver Framework</a>
    {{-- Shortcut opens the edit form to adjust this goal. --}}
    <a href="{{ route('contents.edit', $content) }}">Editar</a>
    {{-- Form element sends the captured data to the specified endpoint. --}}
    <form method="POST" action="{{ route('contents.destroy', $content) }}" style="display:inline">
        @csrf
        @method('DELETE')
        {{-- Button element of type 'submit' to trigger the intended action. --}}
        <button type="submit">Eliminar</button>
    </form>
@endsection

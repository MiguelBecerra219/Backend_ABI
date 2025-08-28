@extends('layouts.app')

@section('content')
    <h1>{{ $framework->name }}</h1>
    <p>{{ $framework->description }}</p>
    <p>{{ $framework->start_year }} - {{ $framework->end_year }}</p>

    <a href="{{ route('frameworks.edit', $framework) }}">Editar</a>
    <form method="POST" action="{{ route('frameworks.destroy', $framework) }}" style="display:inline">
        @csrf
        @method('DELETE')
        <button type="submit">Eliminar</button>
    </form>

    <h2>Objetivos</h2>
    <a href="{{ route('frameworks.contents.create', $framework) }}">Crear objetivo</a>
    <ul>
        @foreach($framework->contentFrameworks as $content)
            <li>
                <a href="{{ route('contents.show', $content) }}">{{ $content->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection

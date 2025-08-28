@extends('layouts.app')

@section('content')
    <h1>Objetivos de {{ $framework->name }}</h1>
    <a href="{{ route('frameworks.contents.create', $framework) }}">Nuevo objetivo</a>
    <ul>
        @foreach($contents as $content)
            <li>
                <a href="{{ route('contents.show', $content) }}">{{ $content->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection

{{--
    View path: contents/index.blade.php.
    Purpose: Renders the index.blade view for the Contents module.
    Expected variables within this template: $content, $contents, $framework.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@extends('layouts.app')

@section('content')
    {{-- Heading introduces the list of objectives tied to the selected framework. --}}
    <h1>Objetivos de {{ $framework->name }}</h1>
    {{-- Link allows the user to register a new objective for this framework. --}}
    <a href="{{ route('frameworks.contents.create', $framework) }}">Nuevo objetivo</a>
    <ul>
        @foreach($contents as $content)
            <li>
                {{-- Each entry links to the detailed view so the objective can be reviewed or edited. --}}
                <a href="{{ route('contents.show', $content) }}">{{ $content->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection

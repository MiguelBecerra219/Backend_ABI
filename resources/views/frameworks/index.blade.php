@extends('layouts.app')

@section('content')
    <h1>Frameworks</h1>
    <a href="{{ route('frameworks.create') }}">Nuevo plan</a>
    <ul>
        @foreach($frameworks as $framework)
            <li>
                <a href="{{ route('frameworks.show', $framework) }}">{{ $framework->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection

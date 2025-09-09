@extends('layouts.app')

@section('content')
<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title m-0">Marcos</h3>
        <a href="{{ route('frameworks.create') }}" class="btn btn-primary">Nuevo Marco</a>
      </div>

      <div class="card-body">
        {{-- Filtros --}}
        <form method="GET" class="row g-2 align-items-end mb-3">
          <div class="col-12 col-md-4">
            <label class="form-label">Buscar (nombre o descripción)</label>
            <div class="input-icon">
              <span class="input-icon-addon"><i class="ti ti-search"></i></span>
              <input
                name="search"
                value="{{ request('search','') }}"
                class="form-control"
                placeholder="Texto a buscar…"
              >
            </div>
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label">Desde (año)</label>
            <input name="year_from" value="{{ request('year_from','') }}" type="number" min="1900" max="2100" class="form-control">
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label">Hasta (año)</label>
            <input name="year_to" value="{{ request('year_to','') }}" type="number" min="1900" max="2100" class="form-control">
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label">Por página</label>
            <select name="per_page" class="form-select">
              @foreach([5,10,25] as $n)
                <option value="{{ $n }}" @selected((int)request('per_page',10) === $n)>{{ $n }}</option>
              @endforeach
            </select>
          </div>

          {{-- Botones Buscar y Limpiar alineados --}}
          <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
            <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
          </div>
        </form>

        {{-- Tabla --}}
        <div class="table-responsive">
          <table class="table table-vcenter table-hover card-table">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Periodo</th>
                <th class="w-25">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($frameworks as $f)
              <tr>
                <td class="text-reset">{{ $f->name }}</td>
                <td>{{ $f->start_year ?? '—' }} – {{ $f->end_year ?? '—' }}</td>
                <td>
                  <div class="d-flex gap-2">
                    <a href="{{ route('frameworks.show', $f) }}" class="btn btn-outline-primary">Ver</a>
                    <a href="{{ route('frameworks.edit', $f) }}" class="btn btn-outline-primary">Editar</a>
                    <form action="{{ route('frameworks.destroy', $f) }}" method="POST" onsubmit="return confirm('¿Eliminar este marco y sus contenidos?')" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-outline-danger">Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-secondary">Sin marcos aún.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
          {{ $frameworks->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

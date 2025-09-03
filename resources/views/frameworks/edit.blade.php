@extends('layouts.app')

@section('title','Editar Marco')

@section('content')
<div class="page-body">
  <div class="container-xl">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-8">

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Editar Marco — <span class="text-secondary">{{ $framework->name }}</span></h3>
          </div>
          <form method="POST" action="{{ route('frameworks.update', $framework) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
              <div class="row g-3">

                {{-- Nombre --}}
                <div class="col-12">
                  <label for="name" class="form-label required">Nombre</label>
                  <input type="text" id="name" name="name"
                         class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name', $framework->name) }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Descripción --}}
                <div class="col-12">
                  <label for="description" class="form-label">Descripción</label>
                  <textarea id="description" name="description"
                            class="form-control @error('description') is-invalid @enderror"
                            rows="3">{{ old('description', $framework->description) }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Año Inicio --}}
                <div class="col-12 col-md-6">
                  <label for="start_year" class="form-label">Año inicio</label>
                  <input type="number" id="start_year" name="start_year" min="1900" max="2100"
                         class="form-control @error('start_year') is-invalid @enderror"
                         value="{{ old('start_year', $framework->start_year) }}">
                  @error('start_year')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                {{-- Año Fin --}}
                <div class="col-12 col-md-6">
                  <label for="end_year" class="form-label">Año fin</label>
                  <input type="number" id="end_year" name="end_year" min="1900" max="2100"
                         class="form-control @error('end_year') is-invalid @enderror"
                         value="{{ old('end_year', $framework->end_year) }}">
                  @error('end_year')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

              </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
              <a href="{{ route('frameworks.index') }}" class="btn btn-outline-secondary">← Volver</a>
              <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

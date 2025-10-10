{{--
    Determine whether the form is being used for creation or editing so the submit
    button text can reflect the proper action.
--}}
@php
    $isEdit = isset($department) && $department->exists;
@endphp

{{-- Wrapper for the department name input field and its validation feedback. --}}
<div class="mb-3">
    {{-- Label clarifies the required nature of the department name field. --}}
    <label for="name" class="form-label required">Nombre del departamento</label>
    {{--
        Text input where administrators provide the official department name.
        Old input is preserved to avoid data loss when validation fails, and the
        field is marked invalid when server-side validation reports an error.
    --}}
    {{-- Input element used to capture the 'name' value. --}}
    <input type="text"
           id="name"
           name="name"
           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
           maxlength="100"
           value="{{ old('name', $department->name ?? '') }}"
           placeholder="Ej: Antioquia"
           required>
    {{-- Validation error message displayed when the name field fails validation. --}}
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    {{-- Helper text provides guidance about the type of value expected. --}}
    <small class="form-hint">Introduce el nombre oficial del departamento administrativo.</small>
</div>

<hr class="my-4">

{{-- Footer containing navigation back to the list and the contextual submit button. --}}
<div class="form-footer d-flex justify-content-between align-items-center">
    {{-- Cancel button returns the user to the department index without saving changes. --}}
    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
        Cancelar
    </a>

    {{-- Submit button either creates or updates a department based on the view context. --}}
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12l5 5l10 -10" />
        </svg>
        {{ $isEdit ? 'Actualizar departamento' : 'Crear departamento' }}
    </button>
</div>

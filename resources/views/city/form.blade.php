{{--
    View path: city/form.blade.php.
    Purpose: Renders the form.blade view for the City module.
    Expected variables within this template: $city, $departmentName, $departments, $errors, $id, $isEdit, $message.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@php
    $isEdit = isset($city) && $city->exists;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            {{-- Label describing the purpose of 'Nombre de la ciudad'. --}}
            <label for="name" class="form-label required">Nombre de la ciudad</label>
            {{-- Input element used to capture the 'name' value. --}}
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   maxlength="100"
                   value="{{ old('name', $city->name ?? '') }}"
                   placeholder="Ej: Medellín"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Nombre oficial de la ciudad en el departamento seleccionado.</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            {{-- Label describing the purpose of 'Departamento'. --}}
            <label for="department_id" class="form-label required">Departamento</label>
            {{-- Dropdown presenting the available options for 'department_id'. --}}
            <select id="department_id"
                    name="department_id"
                    class="form-select {{ $errors->has('department_id') ? 'is-invalid' : '' }}"
                    required>
                <option value="" disabled {{ old('department_id', $city->department_id ?? '') === '' ? 'selected' : '' }}>Selecciona un departamento…</option>
                @foreach($departments as $id => $departmentName)
                    <option value="{{ $id }}" {{ (string)old('department_id', $city->department_id ?? '') === (string)$id ? 'selected' : '' }}>
                        {{ $departmentName }}
                    </option>
                @endforeach
            </select>
            @error('department_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">La ciudad se asociará automáticamente al departamento elegido.</small>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="form-footer d-flex justify-content-between align-items-center">
    {{-- Secondary action lets the user abandon the form without saving changes. --}}
    <a href="{{ route('cities.index') }}" class="btn btn-outline-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
        Cancelar
    </a>

    {{-- Button element of type 'submit' to trigger the intended action. --}}
    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12l5 5l10 -10" />
        </svg>
        {{ $isEdit ? 'Actualizar ciudad' : 'Crear ciudad' }}
    </button>
</div>

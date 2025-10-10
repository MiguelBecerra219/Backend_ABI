{{--
    View path: programs/form.blade.php.
    Purpose: Renders the form.blade view for the Programs module.
    Expected variables within this template: $errors, $groupName, $id, $isEdit, $message, $program, $researchGroups.
    No additional partials are included within this file.
    All markup below follows Tablar styling conventions for visual consistency.
--}}
@php
    $isEdit = isset($program) && $program->exists;
@endphp

<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            {{-- Label describing the purpose of 'Código del programa'. --}}
            <label for="code" class="form-label required">Código del programa</label>
            {{-- Input element used to capture the 'code' value. --}}
            <input type="number"
                   id="code"
                   name="code"
                   class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}"
                   min="1"
                   value="{{ old('code', $program->code ?? '') }}"
                   placeholder="Ej: 1203"
                   required>
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Código institucional del programa (solo números).</small>
        </div>
    </div>
    <div class="col-md-8">
        <div class="mb-3">
            {{-- Label describing the purpose of 'Nombre del programa'. --}}
            <label for="name" class="form-label required">Nombre del programa</label>
            {{-- Input element used to capture the 'name' value. --}}
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   maxlength="100"
                   value="{{ old('name', $program->name ?? '') }}"
                   placeholder="Ej: Licenciatura en Educación Bilingüe"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-hint">Introduce el nombre oficial del programa bilingüe.</small>
        </div>
    </div>
</div>

<div class="mb-3">
    {{-- Label describing the purpose of 'Grupo de investigación'. --}}
    <label for="research_group_id" class="form-label required">Grupo de investigación</label>
    {{-- Dropdown presenting the available options for 'research_group_id'. --}}
    <select id="research_group_id"
            name="research_group_id"
            class="form-select {{ $errors->has('research_group_id') ? 'is-invalid' : '' }}"
            required>
        <option value="" disabled {{ old('research_group_id', $program->research_group_id ?? '') === '' ? 'selected' : '' }}>Selecciona un grupo…</option>
        @foreach($researchGroups as $id => $groupName)
            <option value="{{ $id }}" {{ (string)old('research_group_id', $program->research_group_id ?? '') === (string)$id ? 'selected' : '' }}>
                {{ $groupName }}
            </option>
        @endforeach
    </select>
    @error('research_group_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">El grupo determina las líneas y áreas temáticas disponibles para el programa.</small>
</div>

<hr class="my-4">

<div class="form-footer d-flex justify-content-between align-items-center">
    <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary">
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
        {{ $isEdit ? 'Actualizar programa' : 'Crear programa' }}
    </button>
</div>

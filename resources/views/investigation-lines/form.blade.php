@php
    $isEdit = isset($investigationLine) && $investigationLine->exists;
    $selectedGroup = old('research_group_id', $investigationLine->research_group_id ?? request('research_group_id'));
@endphp

<div class="mb-3">
    <label for="name" class="form-label required">Nombre de la línea</label>
    <input type="text"
           id="name"
           name="name"
           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
           maxlength="100"
           value="{{ old('name', $investigationLine->name ?? '') }}"
           placeholder="Ej: Innovación en educación bilingüe"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Introduce un nombre representativo y único para la línea de investigación.</small>
</div>

<div class="mb-3">
    <label for="description" class="form-label required">Descripción</label>
    <textarea id="description"
              name="description"
              rows="4"
              class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
              placeholder="Describe el enfoque, objetivos y alcance de la línea"
              required>{{ old('description', $investigationLine->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Especifica el impacto esperado, las temáticas y metodologías que se abordan.</small>
</div>

<div class="mb-3">
    <label for="research_group_id" class="form-label required">Grupo de investigación</label>
    <select id="research_group_id"
            name="research_group_id"
            class="form-select {{ $errors->has('research_group_id') ? 'is-invalid' : '' }}"
            required>
        <option value="" disabled {{ empty($selectedGroup) ? 'selected' : '' }}>Selecciona un grupo…</option>
        @foreach($researchGroups as $id => $groupName)
            <option value="{{ $id }}" {{ (string)$selectedGroup === (string)$id ? 'selected' : '' }}>{{ $groupName }}</option>
        @endforeach
    </select>
    @error('research_group_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-hint">Las áreas temáticas disponibles dependerán del grupo seleccionado.</small>
</div>

<hr class="my-4">

<div class="form-footer d-flex justify-content-between align-items-center">
    <a href="{{ route('investigation-lines.index') }}" class="btn btn-outline-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
        Cancelar
    </a>

    <button type="submit" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12l5 5l10 -10" />
        </svg>
        {{ $isEdit ? 'Actualizar línea' : 'Crear línea' }}
    </button>
</div>
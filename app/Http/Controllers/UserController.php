<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffCityProgram;
use App\Models\ResearchStaff\ResearchStaffProfessor;
use App\Models\ResearchStaff\ResearchStaffResearchStaff;
use App\Models\ResearchStaff\ResearchStaffStudent;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Obtener parámetros de filtrado y búsqueda
        $search = $request->get('search');
        $role = $request->get('role');
        $state = $request->get('state');
        $cityProgramId = $request->get('city_program_id');
        $perPageOptions = [10, 20, 30];
        $perPage = (int) $request->get('per_page', $perPageOptions[0]);

        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = $perPageOptions[0];
        }

        // Query base
        $query = ResearchStaffUser::query();

        // Aplicar filtros
        if ($role) {
            $query->where('role', $role);
        }

        if ($state) {
            $query->where('state', $state);
        }

        if ($cityProgramId) {
            $studentIds = ResearchStaffStudent::where('city_program_id', $cityProgramId)->pluck('user_id');
            $professorIds = ResearchStaffProfessor::where('city_program_id', $cityProgramId)->pluck('user_id');
            
            $query->where(function ($q) use ($studentIds, $professorIds) {
                $q->whereIn('id', $studentIds)
                    ->orWhereIn('id', $professorIds);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%');
                
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
                
                $searchTerm = '%' . $search . '%';
                
                $studentIds = ResearchStaffStudent::where('name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('card_id', 'like', $searchTerm)
                    ->pluck('user_id');
                    
                $professorIds = ResearchStaffProfessor::where('name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('card_id', 'like', $searchTerm)
                    ->pluck('user_id');
                    
                $researchStaffIds = ResearchStaffResearchStaff::where('name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('card_id', 'like', $searchTerm)
                    ->pluck('user_id');
                    
                $q->orWhereIn('id', $studentIds)
                    ->orWhereIn('id', $professorIds)
                    ->orWhereIn('id', $researchStaffIds);
            });
        }

        // ⚠️ CORRECCIÓN AQUÍ: Eliminar el orderBy('name')
        // Solo ordenar por created_at en la consulta SQL
        $query->orderBy('created_at', 'desc');

        // Obtener los usuarios paginados
        $users = $query->paginate($perPage);
        $users->appends($request->query());

        // ✅ ORDENAR POR NOMBRE EN PHP (después de obtener los resultados)
        $usersCollection = $users->getCollection()->sortBy(function ($user) {
            switch ($user->role) {
                case 'student':
                    return $user->details->name ?? '';
                case 'professor':
                case 'committee_leader':
                    return $user->details->name ?? '';
                case 'research_staff':
                    return $user->details->name ?? '';
                default:
                    return '';
            }
        });

        // Reemplazar la colección paginada con la colección ordenada
        $users->setCollection($usersCollection);

        // Cargar detalles de usuarios
        $userIds = collect($users->items())->pluck('id')->toArray();
        $studentIds = [];
        $professorIds = [];
        $researchStaffIds = [];

        foreach ($users as $user) {
            switch ($user->role) {
                case 'student':
                    $studentIds[] = $user->id;
                    break;
                case 'professor':
                case 'committee_leader':
                    $professorIds[] = $user->id;
                    break;
                case 'research_staff':
                    $researchStaffIds[] = $user->id;
                    break;
            }
        }

        $students = ResearchStaffStudent::whereIn('user_id', $studentIds)->get()->keyBy('user_id');
        $professors = ResearchStaffProfessor::whereIn('user_id', $professorIds)->get()->keyBy('user_id');
        $researchStaffs = ResearchStaffResearchStaff::whereIn('user_id', $researchStaffIds)->get()->keyBy('user_id');

        foreach ($users as $user) {
            switch ($user->role) {
                case 'student':
                    $user->details = $students[$user->id] ?? null;
                    break;
                case 'professor':
                case 'committee_leader':
                    $user->details = $professors[$user->id] ?? null;
                    break;
                case 'research_staff':
                    $user->details = $researchStaffs[$user->id] ?? null;
                    break;
            }
        }

        // Cargar programas
        $cityPrograms = ResearchStaffCityProgram::all();
        foreach ($cityPrograms as $program) {
            $program->full_name = $program->program->name . ' - ' . $program->city->name;
        }

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'state' => $state,
            'cityProgramId' => $cityProgramId,
            'cityPrograms' => $cityPrograms,
            'perPage' => $perPage,
            'perPageOptions' => $perPageOptions,
            'i' => ($users->currentPage() - 1) * $users->perPage(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ResearchStaffUser $user): View
    {
        
        // Cargar los datos adicionales según el rol
        $details = null;
        
        switch ($user->role) {
            case 'student':
                // Cargar relaciones necesarias para city_program
                $details = ResearchStaffStudent::with([
                    'cityProgram' => function($query) {
                        $query->with(['city', 'program']);
                    }
                ])->where('user_id', $user->id)->first();
                break;
                
            case 'professor':
            case 'committee_leader':
                // Cargar relaciones necesarias para city_program
                $details = ResearchStaffProfessor::with([
                    'cityProgram' => function($query) {
                        $query->with(['city', 'program']);
                    }
                ])->where('user_id', $user->id)->first();
                break;
                
            case 'research_staff':
                $details = ResearchStaffResearchStaff::where('user_id', $user->id)->first();
                break;
        }
        
        // Pasar tanto el usuario como sus detalles a la vista
        return view('users.show', compact('user', 'details'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResearchStaffUser $user): \Illuminate\View\View
    {
        // Cargar los datos adicionales según el rol
        $details = null;
        $cityPrograms = ResearchStaffCityProgram::all();
        
        foreach ($cityPrograms as $program) {
            $program->full_name = $program->program->name . ' - ' . $program->city->name;
        }

        switch ($user->role) {
            case 'student':
                $details = ResearchStaffStudent::where('user_id', $user->id)->first();
                break;
                
            case 'professor':
            case 'committee_leader':
                $details = ResearchStaffProfessor::where('user_id', $user->id)->first();
                break;
                
            case 'research_staff':
                $details = ResearchStaffResearchStaff::where('user_id', $user->id)->first();
                break;
        }

        return view('users.edit', [
            'user' => $user,
            'details' => $details,
            'cityPrograms' => $cityPrograms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\Illuminate\Http\Request $request, ResearchStaffUser $user): RedirectResponse
    {
        // Validar campos comunes
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'state' => 'required|in:active,inactive',
            'role' => 'required|in:student,professor,committee_leader,research_staff',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $newRole = $request->role;
        $oldRole = $user->role;
        
        // Si hay cambio de rol, validamos los campos del nuevo rol
        if ($newRole !== $oldRole) {
            $additional = $this->validateRoleFields($request, $newRole);
        } 
        // Si el rol no cambia, validamos los campos del rol actual
        else {
            $additional = $this->validateRoleFields($request, $newRole);
        }

        // Actualizar el usuario
        $user->update([
            'email' => $validated['email'],
            'state' => $validated['state'],
            'role' => $newRole,
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        // Si hay cambio de rol, necesitamos manejar la información de las tablas de rol
        if ($newRole !== $oldRole) {
            // Eliminar (soft delete) el registro de la tabla anterior
            $this->deleteOldRoleRecord($user, $oldRole);
            
            // Crear un nuevo registro en la tabla del nuevo rol
            $this->createNewRoleRecord($user, $newRole, $additional);
        } 
        // Si el rol no cambia, actualizamos el registro existente
        else {
            $this->updateExistingRoleRecord($user, $newRole, $additional);
        }

        return redirect()
            ->route('users.index')
            ->with('success', "Usuario '{$user->email}' actualizado correctamente.");
    }

    /**
     * Valida los campos específicos según el rol
     */
    private function validateRoleFields(\Illuminate\Http\Request $request, string $role): array
    {
        switch ($role) {
            case 'student':
                return $request->validate([
                    'card_id' => 'required|string|max:20',
                    'name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'semester' => 'required|integer|min:1|max:10',
                    'city_program_id' => 'required|exists:city_programs,id',
                ]);
                
            case 'professor':
            case 'committee_leader':
                $validated = $request->validate([
                    'card_id' => 'required|string|max:20',
                    'name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'city_program_id' => 'required|exists:city_programs,id',
                ]);
                
                // ✅ ESTO ES LO MÁS IMPORTANTE:
                // Ignoramos lo que el cliente envió y establecemos basado en el rol
                $validated['committee_leader'] = ($role === 'committee_leader') ? 1 : 0;
                
            case 'research_staff':
                return $request->validate([
                    'card_id' => 'required|string|max:20',
                    'name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                ]);
                
            default:
                throw new \Exception('Rol no válido');
        }
    }

    /**
     * Elimina el registro de la tabla del rol anterior
     */
    private function deleteOldRoleRecord(ResearchStaffUser $user, string $role): void
    {
        switch ($role) {
            case 'student':
                ResearchStaffStudent::where('user_id', $user->id)->delete();
                break;
                
            case 'professor':
            case 'committee_leader':
                ResearchStaffProfessor::where('user_id', $user->id)->delete();
                break;
                
            case 'research_staff':
                ResearchStaffResearchStaff::where('user_id', $user->id)->delete();
                break;
        }
    }

    /**
     * Crea un nuevo registro en la tabla del nuevo rol
     */
    private function createNewRoleRecord(ResearchStaffUser $user, string $role, array $data): void
    {
        switch ($role) {
            case 'student':
                ResearchStaffStudent::create([
                    'card_id' => $data['card_id'],
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'semester' => $data['semester'],
                    'city_program_id' => $data['city_program_id'],
                    'user_id' => $user->id,
                ]);
                break;
                
            case 'professor':
            case 'committee_leader':
                ResearchStaffProfessor::create([
                    'card_id' => $data['card_id'],
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'committee_leader' => $data['committee_leader'],
                    'city_program_id' => $data['city_program_id'],
                    'user_id' => $user->id,
                ]);
                break;
                
            case 'research_staff':
                ResearchStaffResearchStaff::create([
                    'card_id' => $data['card_id'],
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'user_id' => $user->id,
                ]);
                break;
        }
    }

    /**
     * Actualiza el registro existente en la tabla del rol
     */
    private function updateExistingRoleRecord(ResearchStaffUser $user, string $role, array $data): void
    {
        switch ($role) {
            case 'student':
                $student = ResearchStaffStudent::where('user_id', $user->id)->first();
                if ($student) {
                    $student->update([
                        'card_id' => $data['card_id'],
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                        'semester' => $data['semester'],
                        'city_program_id' => $data['city_program_id'],
                    ]);
                }
                break;
                
            case 'professor':
            case 'committee_leader':
                $professor = ResearchStaffProfessor::where('user_id', $user->id)->first();
                if ($professor) {
                    $professor->update([
                        'card_id' => $data['card_id'],
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                        'committee_leader' => ($role === 'committee_leader') ? 1 : 0,
                        'city_program_id' => $data['city_program_id'],
                    ]);
                }
                break;
                
            case 'research_staff':
                $researchStaff = ResearchStaffResearchStaff::where('user_id', $user->id)->first();
                if ($researchStaff) {
                    $researchStaff->update([
                        'card_id' => $data['card_id'],
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                    ]);
                }
                break;
        }
    }

    /**
     * Inactivate the specified user
     */
    public function destroy(ResearchStaffUser $user): RedirectResponse
    {
        // Iniciar transacción para mantener consistencia
        DB::transaction(function () use ($user) {
            // 1. Cambiar estado del usuario a inactivo (0)
            $user->update(['state' => '0']);
            
            // 2. Aplicar soft delete en la tabla relacionada según el rol
            switch ($user->role) {
                case 'student':
                    $student = ResearchStaffStudent::where('user_id', $user->id)->first();
                    if ($student) {
                        $student->delete(); // Esto llama a soft delete si el modelo usa SoftDeletes
                    }
                    break;
                    
                case 'professor':
                case 'committee_leader':
                    $professor = ResearchStaffProfessor::where('user_id', $user->id)->first();
                    if ($professor) {
                        $professor->delete(); // Esto llama a soft delete si el modelo usa SoftDeletes
                    }
                    break;
                    
                case 'research_staff':
                    $researchStaff = ResearchStaffResearchStaff::where('user_id', $user->id)->first();
                    if ($researchStaff) {
                        $researchStaff->delete(); // Esto llama a soft delete si el modelo usa SoftDeletes
                    }
                    break;
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', "Usuario '{$user->email}' ha sido desactivado correctamente.");
    }

    /**
     * Reactivate the specified user
     */
    public function activate(ResearchStaffUser $user): RedirectResponse
    {
        DB::transaction(function () use ($user) {
            // 1. Primero restaurar los registros relacionados
            $restored = false;
            
            switch ($user->role) {
                case 'student':
                    $student = ResearchStaffStudent::withTrashed()
                        ->where('user_id', $user->id)
                        ->first();
                        
                    if ($student && $student->trashed()) {
                        $student->restore();
                        $restored = true;
                    }
                    break;
                    
                case 'professor':
                case 'committee_leader':
                    $professor = ResearchStaffProfessor::withTrashed()
                        ->where('user_id', $user->id)
                        ->first();
                        
                    if ($professor && $professor->trashed()) {
                        $professor->restore();
                        $restored = true;
                    }
                    break;
                    
                case 'research_staff':
                    $researchStaff = ResearchStaffResearchStaff::withTrashed()
                        ->where('user_id', $user->id)
                        ->first();
                        
                    if ($researchStaff && $researchStaff->trashed()) {
                        $researchStaff->restore();
                        $restored = true;
                    }
                    break;
            }
            
            // 2. Solo actualizar el estado si se restauró algún registro relacionado
            // Esto es importante para evitar inconsistencias
            if ($restored) {
                $user->update(['state' => '1']);
            } else {
                // Si no había registros relacionados eliminados, podría ser un error
                // Podrías lanzar una excepción o registrar un log
                // \log::warning("Intento de activar usuario {$user->id} pero no se encontraron registros relacionados eliminados");
            }
        });

        return redirect()
            ->route('users.index')
            ->with('success', "Usuario '{$user->email}' ha sido reactivado correctamente.");
    }
}

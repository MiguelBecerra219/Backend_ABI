<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResearchStaff\ResearchStaffCityProgram;
use App\Models\ResearchStaff\ResearchStaffProfessor;
use App\Models\ResearchStaff\ResearchStaffResearchStaff;
use App\Models\ResearchStaff\ResearchStaffStudent;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        // El middleware se maneja en las rutas
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role !== 'research_staff') {
                return redirect('/home')->with('error', 'Unauthorized access');
            }
            return $next($request);
        });
    }

    public function showRegistrationForm()
    {
        // Cargar programas para la vista
        $cityPrograms = ResearchStaffCityProgram::all();
        foreach ($cityPrograms as $program) {
            $program->full_name = $program->program->name . ' - ' . $program->city->name;
        }

        return view('auth.register', compact('cityPrograms'));
    }

    protected function validator(array $data)
    {
    $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.string' => 'El nombre debe ser un texto válido.',
        'name.max' => 'El nombre no debe exceder 255 caracteres.',
        
        'last_name.required' => 'El apellido es obligatorio.',
        'last_name.string' => 'El apellido debe ser un texto válido.',
        'last_name.max' => 'El apellido no debe exceder 255 caracteres.',
        
        'phone.required' => 'El teléfono es obligatorio.',
        'phone.string' => 'El teléfono debe ser un texto válido.',
        'phone.max' => 'El teléfono no debe exceder 20 caracteres.',
        
        'password.required' => 'La contraseña es obligatoria.',
        'password.string' => 'La contraseña debe ser un texto válido.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        
        'role.required' => 'El rol es obligatorio.',
        'role.in' => 'El rol seleccionado no es válido.',
        
        'card_id.required' => 'La cédula es obligatoria.',
        'card_id.string' => 'La cédula debe ser un texto válido.',
        'card_id.max' => 'La cédula no debe exceder 20 caracteres.',
        
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Debe ingresar un correo electrónico válido.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        
        'city_program_id.required' => 'El programa es obligatorio.',
        'city_program_id.exists' => 'El programa seleccionado no existe.',
        
        'semester.required' => 'El semestre es obligatorio.',
        'semester.integer' => 'El semestre debe ser un número entero.',
        'semester.min' => 'El semestre mínimo es 1.',
        'semester.max' => 'El semestre máximo es 10.',
    ];

        // Validación básica común
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],            
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'role' => ['required', 'in:student,professor,committee_leader,research_staff'],
            'card_id' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\ResearchStaff\ResearchStaffStudent::where('card_id', $value)->exists() ||
                            \App\Models\ResearchStaff\ResearchStaffProfessor::where('card_id', $value)->exists() ||
                            \App\Models\ResearchStaff\ResearchStaffResearchStaff::where('card_id', $value)->exists();

                    if ($exists) {
                        $fail("El número de identificación ya ha sido registrado.");
                    }
                },
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ];

        // Validación adicional según el rol
        if (in_array($data['role'], ['student', 'professor', 'committee_leader'])) {
            $rules['city_program_id'] = ['required', 'exists:city_program,id'];
        }

        if ($data['role'] === 'student') {
            $rules['semester'] = ['required', 'integer', 'min:1', 'max:10'];
        }

        return Validator::make($data, $rules, $messages);
    }

    protected function create(array $data)
    {
        // Crear el usuario
        $user = ResearchStaffUser::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role']
        ]);

        // Crear registro en la tabla correspondiente según el rol
        switch ($data['role']) {
            case 'student':
                $student = new ResearchStaffStudent();
                $student-> card_id = $data['card_id'];
                $student-> name = $data['name'];
                $student-> last_name = $data['last_name'];
                $student-> phone = $data['phone'];
                $student-> semester = $data['semester'];
                $student-> city_program_id = $data['city_program_id'];
                $student-> user_id = $user->id;
                $student-> save();
                break;
                
            case 'professor':
            case 'committee_leader':
                $professor = new ResearchStaffProfessor();
                $professor-> card_id = $data['card_id'];
                $professor-> name = $data['name'];
                $professor-> last_name = $data['last_name'];
                $professor-> phone = $data['phone'];
                $professor->committee_leader = $data['role'] === 'committee_leader' ? 1 : 0;
                $professor-> city_program_id = $data['city_program_id'];
                $professor-> user_id = $user->id;
                $professor-> save();
                break;
                
            case 'research_staff':
                $research_staff = new ResearchStaffResearchStaff();
                $research_staff-> card_id = $data['card_id'];
                $research_staff-> name = $data['name'];
                $research_staff-> last_name = $data['last_name'];
                $research_staff-> phone = $data['phone'];
                $research_staff-> user_id = $user->id;
                $research_staff-> save();
                break;
        }

        return $user;
    }

    public function register(Request $request)
    {
        // Validar los datos
        $this->validator($request->all())->validate();

        // Crear el usuario (sin loguearlo)
        $user = $this->create($request->all());

        // Disparar evento de registro (opcional, para mantener consistencia)
        event(new Registered($user));

        // Llamar a tu método registered() que maneja la redirección y el mensaje
        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        // Fallback (esto no debería ejecutarse si registered() devuelve una respuesta)
        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * Sobrescribe el comportamiento después del registro
     */
    protected function registered(Request $request, $user)
    {
        return redirect()->route('register')->with('success', 'Usuario ' . $user->name . ' registrado exitosamente.');
    }
}

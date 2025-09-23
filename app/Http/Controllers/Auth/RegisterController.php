<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CityProgram;
use App\Models\Professor;
use App\Models\ResearchStaff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use RegistersUsers;

    //protected $redirectTo = '/register';

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
        $cityPrograms = CityProgram::all();
        foreach ($cityPrograms as $program) {
            $program->full_name = $program->program->name . ' - ' . $program->city->name;
        }

        return view('auth.register', compact('cityPrograms'));
    }

    protected function validator(array $data)
    {
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
                    $exists = \App\Models\Student::where('card_id', $value)->exists() ||
                            \App\Models\Professor::where('card_id', $value)->exists() ||
                            \App\Models\ResearchStaff::where('card_id', $value)->exists();

                    if ($exists) {
                        $fail("The {$attribute} has already been taken.");
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

        return Validator::make($data, $rules);
    }

    protected function create(array $data)
    {
        // Crear el usuario
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role']
        ]);

        // Crear registro en la tabla correspondiente según el rol
        switch ($data['role']) {
            case 'student':
                $student = new Student();
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
                $professor = new Professor();
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
                $research_staff = new ResearchStaff();
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

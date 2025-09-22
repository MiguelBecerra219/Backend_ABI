@extends('tablar::auth.layout')
@section('title', 'Register')
@section('content')
    <div class="container container-tight py-4">
        <div class="text-center mb-1 mt-5">
            <a href="" class="navbar-brand navbar-brand-autodark">
                <img src="{{asset(config('tablar.auth_logo.img.path','assets/logo.svg'))}}" height="110"
                     alt=""></a>
        </div>
        <form class="card card-md" action="{{route('register')}}" method="post" autocomplete="off" novalidate>
            @csrf
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <h2 class="card-title text-center mb-4">Registra un nuevo usuario</h2>
                
                <!-- Role Selection -->
                <div class="mb-3">
                    <label class="form-label">User Role</label>
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">-- Select Role --</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="professor" {{ old('role') == 'professor' ? 'selected' : '' }}>Professor</option>
                        <option value="committee_leader" {{ old('role') == 'committee_leader' ? 'selected' : '' }}>Committee Leader</option>
                        <option value="research_staff" {{ old('role') == 'research_staff' ? 'selected' : '' }}>Research Staff</option>
                    </select>
                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Personal Information -->
                <div class="mb-3">
                    <label class="form-label">Card ID</label>
                    <input type="text" name="card_id" class="form-control @error('card_id') is-invalid @enderror" 
                           placeholder="Enter identification number" value="{{ old('card_id') }}">
                    @error('card_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           placeholder="Enter first name" value="{{ old('name') }}">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                           placeholder="Enter last name" value="{{ old('last_name') }}">
                    @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                           placeholder="Enter phone number" value="{{ old('phone') }}">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Student specific fields -->
                <div id="student-fields" class="role-fields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Semester</label>
                        <input type="number" name="semester" class="form-control @error('semester') is-invalid @enderror" 
                               placeholder="Enter semester (1-10)" min="1" max="10" value="{{ old('semester') }}">
                        @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Professor specific fields -->
                <div id="professor-fields" class="role-fields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Committee Leader</label>
                        <select name="committee_leader" class="form-select @error('committee_leader') is-invalid @enderror">
                            <option value="0" {{ (old('committee_leader') ?? '0') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ (old('committee_leader') ?? '0') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('committee_leader')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Common fields for professors, committee leaders and students -->
                <div id="program-fields" class="role-fields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">City Program</label>
                        <select name="city_program_id" class="form-select @error('city_program_id') is-invalid @enderror">
                            <option value="">-- Select Program --</option>
                            @foreach($cityPrograms as $program)
                                <option value="{{ $program->id }}" {{ old('city_program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_program_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Email and Password -->
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Enter email" value="{{ old('email') }}">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group input-group-flat">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Password" autocomplete="off">
                        <span class="input-group-text">
                          <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                              <circle cx="12" cy="12" r="2"/>
                              <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                            </svg>
                          </a>
                        </span>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group input-group-flat">
                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" 
                               placeholder="Confirm Password" autocomplete="off">
                        <span class="input-group-text">
                          <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                              <circle cx="12" cy="12" r="2"/>
                              <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"/>
                            </svg>
                          </a>
                        </span>
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" required/>
                        <span class="form-check-label">Agree to the terms and policy.</span>
                    </label>
                </div>
                
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create new account</button>
                </div>
            </div>
        </form>
        <div class="text-center text-muted mt-3">
            Already have account? <a href="{{route('login')}}" tabindex="-1">Sign in</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const studentFields = document.getElementById('student-fields');
            const professorFields = document.getElementById('professor-fields');
            const programFields = document.getElementById('program-fields');
            
            function toggleFields() {
                const role = roleSelect.value;
                
                // Reset all fields
                studentFields.style.display = 'none';
                professorFields.style.display = 'none';
                programFields.style.display = 'none';
                
                // Show relevant fields based on role
                if (role === 'student') {
                    studentFields.style.display = 'block';
                    programFields.style.display = 'block';
                } else if (role === 'professor' || role === 'committee_leader') {
                    professorFields.style.display = 'block';
                    programFields.style.display = 'block';
                    
                    // If committee_leader role, set committee_leader to 1
                    if (role === 'committee_leader') {
                        document.querySelector('select[name="committee_leader"]').value = '1';
                    }
                }
            }
            
            // Initial check
            toggleFields();
            
            // Add event listener
            roleSelect.addEventListener('change', toggleFields);
        });
    </script>
@endsection
<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'cedula' => ['required', 'string', 'max:20', 'unique:users'],
            'carrera_id' => ['required', 'exists:carreras,id'],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'cedula' => $input['cedula'],
            'carrera_id' => $input['carrera_id'],
            'is_active' => true,
            'password' => Hash::make($input['password']),
        ]);

        $user->assignRole('estudiante');

        return $user;
    }
}
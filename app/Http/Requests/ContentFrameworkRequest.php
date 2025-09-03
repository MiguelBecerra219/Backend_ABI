<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentFrameworkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => ['required','string','max:255'],
            // BD no admite null ⇒ aceptamos nullable pero lo convertimos a '' en el controlador
            'description' => ['nullable','string'],
        ];
    }
}

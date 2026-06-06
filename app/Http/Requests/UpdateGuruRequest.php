<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id'); 

        return [
            'name'    => 'required|string|max:255',
            'nip'     => 'required|unique:users,nip,' . $id,
            'email'   => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required'
        ];
    }
}
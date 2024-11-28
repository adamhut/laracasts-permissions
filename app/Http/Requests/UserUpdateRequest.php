<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'roles' => ['nullable','array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            // 'email' => [
            //     'required',
            //     'string',
            //     'lowercase',
            //     'email',
            //     'max:255',
            //     Rule::unique(User::class)->ignore($this->user()->id),
            // ],
        ];
    }


    public function withValidator($validator){
        $validator->after(function ($validator) {
            if (!$this->user()->id === $this->route('user')->id) {
                $adminRoleId = Role::where('name','admin')->first()->id;

                if ( !in_array($adminRoleId,$this->input('roles',[]))){
                    $validator->errors()->add('roles','You can not remove admin role from your slef');
                }
            }
        })        ;
    }

}

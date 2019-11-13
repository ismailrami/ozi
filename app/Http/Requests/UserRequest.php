<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'driver_licence' => 'required',
            'function' => 'required',
            'source' => 'required',
            'pre_sal' => 'required',
            'act_sal' => 'required',
            'disponibility' => 'required',
            'status' => 'required'
        ];
    }
}

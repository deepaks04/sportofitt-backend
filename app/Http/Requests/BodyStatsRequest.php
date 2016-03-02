<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class BodyStatsRequest extends Request {

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
            'weight' => 'required',
            'height' => 'required',
            'waist' => 'required',
            'chest' => 'required',
            'forarm' => 'required',
            'wrist' => 'required',
            'hip' => 'required',
            'activity_level' => 'required',
        ];
    }

}

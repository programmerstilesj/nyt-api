<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NYTBestSellersRequest extends FormRequest
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
            'author' => 'nullable|string',
            'isbn' => [
                'nullable',
                'string',
                'regex:/^(\d{10}|\d{13})(;\d{10}|\d{13})*$/'
            ],
            'title' => 'nullable|string',
            'offset' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!is_int($value)) {
                        $value = (int) $value;
                    }
                    if ($value < 0 || $value % 20 !== 0) {
                        $fail($attribute.' must be a non-negative multiple of 20.');
                    }
                },
            ],
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}

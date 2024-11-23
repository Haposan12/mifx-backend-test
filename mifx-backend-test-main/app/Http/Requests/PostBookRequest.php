<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
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
        // @TODO implement
        return [
            'isbn' => 'required|string|min:13|max:13|unique:books',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'authors' => 'required|array|min:1',
            'authors.*' => 'required|exists:authors,id',
            'published_year' => 'required|integer|between:1900,2020',
            'price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'isbn.unique' => 'The ISBN must be unique.',
            'isbn.string' => 'The ISBN must be a string.',
            'authors.*.exists' => 'Each author must exist in the authors table.',
        ];
    }
}

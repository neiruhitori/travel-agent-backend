<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestinationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if (request()->isMethod('post')) {
            return [
                'name' => 'required|string',
                'location' => 'required|string',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ];
        } else {
            return [
                'name' => 'required|string',
                'location' => 'required|string',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                'name.required' => 'Name is required',
                'location.required' => 'Location is required',
                'description' => 'Deskription',
                'price.required' => 'Price',
                'image.required' => 'Image is required',
            ];
        } else {
            return [
                'name.required' => 'Name is required',
                'location.required' => 'Location is required',
                'price.required' => 'Price',
            ];
        }
    }
}

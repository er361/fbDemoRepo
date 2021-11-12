<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
{
    const PER_PAGE_DEFAULT = 10;

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
        $perPageRule = 'integer|max:' . env('PAGINATION_MAX_PER_PAGE') . '|min:' . env('PAGINATION_MIN_PER_PAGE');
        return [
            //
            'perPage' => $perPageRule
        ];
    }
}

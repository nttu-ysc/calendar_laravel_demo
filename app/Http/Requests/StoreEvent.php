<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEvent extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!isset(request()->event)) {
            return true;
        }
        if (Auth::id() === request()->event->user_id) {
            return true;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'year' => 'required',
            'month' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ];
    }
}

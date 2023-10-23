<?php

namespace App\Http\Requests;

use App\Consts\CurrencyDecimal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StripeRequest extends FormRequest
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
        $amountRule = 'required';
        if (in_array($this->currency, CurrencyDecimal::ZERO_DECIMAL)) {
            $amountRule .= '|integer';
        } else {
            $amountRule .= '|decimal:0,3';
        }

        return [
            'currency' => 'required|alpha:ascii|size:3',
            'amount'   => $amountRule,
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => Str::upper($this->currency),
        ]);
    }
}

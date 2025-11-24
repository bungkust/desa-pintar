<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateAgendaSearch extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public search is allowed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'in:pemerintahan,kesehatan,lingkungan,budaya,umum'],
            'date' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:255'],
            'view' => ['nullable', 'string', 'in:card,table'],
        ];
    }

    /**
     * Get validated search input with length limit
     */
    public function getValidatedSearch(): ?string
    {
        $search = $this->validated()['search'] ?? null;
        
        if ($search) {
            // Limit search length to prevent DoS
            $search = substr($search, 0, 255);
        }
        
        return $search;
    }
}

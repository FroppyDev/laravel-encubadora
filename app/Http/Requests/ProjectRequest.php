<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:180'],
            'summary' => [$required, 'string', 'max:500'],
            'description' => [$required, 'string', 'max:20000'],
            'category_id' => [$required, 'integer', 'exists:categories,id'],
            'problem_statement' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'proposed_solution' => ['sometimes', 'nullable', 'string', 'max:10000'],
            'repository_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'demo_url' => ['sometimes', 'nullable', 'url:http,https', 'max:2048'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'technology_ids' => ['sometimes', 'array', 'max:30'],
            'technology_ids.*' => ['integer', 'distinct', 'exists:technologies,id'],
        ];
    }
}

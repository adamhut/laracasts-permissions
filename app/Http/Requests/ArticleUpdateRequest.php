<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ArticleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $article = $this->route('article');

        // return Gate::allows('manage-articles', $article);

        $response = Gate::inspect('update', $article);

        if ($response->allowed()) {
            return true;
        }

        throw new ModelNotFoundException();
        // this will get the message from the policy
        // throw new AuthorizationException($response->message());

        // return Gate::allows('update', $article);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required','string'],
        ];
    }
}

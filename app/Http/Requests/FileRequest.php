<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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
        $groupId = $this->route('groupId');

        return [
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx,txt',
                'max:2048',
                // Custom rule to ensure unique file name across the entire `files` table
                function ($attribute, $value, $fail) use ($groupId) {
                    if ($this->hasFile('file')) {
                        $fileName = $this->file('file')->getClientOriginalName();
                        $exists = \App\Models\File::where('name', $fileName)
                            ->where('group_id', $groupId)
                            ->exists();

                        if ($exists) {
                            $fail("The file name '{$fileName}' already exists in this group.");
                        }
                    } else {
                        $fail("No file was uploaded.");
                    }
                }
            ]
        ];
    }


}

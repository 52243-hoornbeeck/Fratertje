<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class MemberTypeRequest extends FormRequest {
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array {
        return [
            'name'=>['required','string','max:100',Rule::unique('member_types','name')->ignore($this->route('member_type'))],
            'description'=>['required','string','max:1000'],
            'price'=>['required','string','regex:/^\d{1,5}(\.\d{1,2})?$/'],
        ];
    }
}

<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class BreedingNumberRequest extends FormRequest {
    public function authorize(): bool { return $this->user() !== null; }
    protected function prepareForValidation(): void {
        if (is_string($this->input('breeding_number'))) { $this->merge(['breeding_number'=>strtoupper(trim($this->input('breeding_number')))]); }
    }
    public function rules(): array {
        $number=$this->route('breeding_number');
        return [
            'member_id'=>$number
                ? ['required','integer',Rule::in([$number->member_id])]
                : ['required','integer',Rule::exists('members','id')->whereNull('deleted_at'),Rule::unique('breeding_numbers','member_id')],
            'breeding_number'=>['required','string','max:30','regex:/^[A-Z0-9-]+$/',Rule::unique('breeding_numbers','breeding_number')->ignore($number?->id)],
            'issue_year'=>['required','integer','between:1900,'.now()->year],
        ];
    }
    public function messages(): array {
        return ['member_id.unique'=>'Dit lid heeft al een kweeknummer, mogelijk in het nummerarchief. Herstel of wijzig dat nummer.'];
    }
}

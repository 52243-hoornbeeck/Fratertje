<?php
namespace App\Http\Requests;

use App\Models\BreedingNumber;
use App\Models\MemberType;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MemberRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('breeding_number'))) {
            $this->merge(['breeding_number' => strtoupper(trim($this->input('breeding_number')))]);
        }
        if (is_string($this->input('postal_code'))) {
            $this->merge(['postal_code' => strtoupper(trim($this->input('postal_code')))]);
        }
    }

    public function rules(): array
    {
        $member = $this->route('member');
        $numberId = $member ? BreedingNumber::withTrashed()->where('member_id', $member->id)->value('id') : null;
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'member_type_id' => ['required', 'integer', Rule::exists('member_types', 'id')->whereIn('code', ['adult', 'youth', 'guest'])],
            'is_active' => ['required', 'boolean'],
            'street' => ['required', 'string', 'max:150'],
            'house_number' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'breeding_number' => ['nullable', 'string', 'max:30', 'regex:/^[A-Z0-9-]+$/', Rule::unique('breeding_numbers', 'breeding_number')->ignore($numberId)],
            'issue_year' => ['nullable', 'integer', 'between:1900,'.now()->year],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->has('member_type_id') || $validator->errors()->has('birth_date')) { return; }
            $type = MemberType::find($this->input('member_type_id'));
            if (! $type) { return; }
            $adult = CarbonImmutable::parse($this->input('birth_date'))->addYears(18)->startOfDay()->lte(today());
            if (($type->code === 'adult' && ! $adult) || ($type->code === 'youth' && $adult)) {
                $validator->errors()->add('member_type_id', 'Kies jeugdlid voor iemand onder de 18 en volwassen lid vanaf 18 jaar, of kies gastlid.');
            }
            if ($type->requiresBreedingNumber()) {
                if (! $this->filled('breeding_number')) { $validator->errors()->add('breeding_number', 'Een jeugd- of volwassen lid heeft verplicht een kweeknummer.'); }
                if (! $this->filled('issue_year')) { $validator->errors()->add('issue_year', 'Vul het uitgiftejaar van het kweeknummer in.'); }
            } elseif ($this->filled('breeding_number') || $this->filled('issue_year')) {
                $validator->errors()->add('breeding_number', 'Een gastlid mag geen kweeknummer of uitgiftejaar hebben. Maak beide velden leeg.');
            }
        }];
    }
}

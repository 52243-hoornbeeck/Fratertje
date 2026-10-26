<?php
namespace App\Services;

use App\Models\Address;
use App\Models\BreedingNumber;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class MemberService
{
    /** Only pass data validated by MemberRequest. Save the complete aggregate atomically. */
    public function save(array $data, ?Member $member = null): Member
    {
        try {
            return DB::transaction(function () use ($data, $member) {
                if ($member) {
                    $member = Member::query()->lockForUpdate()->findOrFail($member->id);
                    $address = $member->address;
                    $address->update(Arr::only($data, ['street', 'house_number', 'postal_code', 'city']));
                } else {
                    $address = Address::create(Arr::only($data, ['street', 'house_number', 'postal_code', 'city']));
                    $member = new Member;
                }
                $member->fill(Arr::only($data, ['first_name', 'last_name', 'birth_date', 'member_type_id', 'is_active']));
                $member->address()->associate($address);
                $member->save();
                $number = BreedingNumber::withTrashed()->where('member_id', $member->id)->first();
                if (MemberType::findOrFail($data['member_type_id'])->requiresBreedingNumber()) {
                    $number ??= new BreedingNumber;
                    $number->member()->associate($member);
                    $number->fill(Arr::only($data, ['breeding_number', 'issue_year']));
                    $number->deleted_at = null;
                    $number->save();
                } elseif ($number && ! $number->trashed()) {
                    $number->delete();
                }
                return $member;
            });
        } catch (QueryException $exception) {
            // A simultaneous request can race with FormRequest's uniqueness check.
            if (in_array((string) $exception->getCode(), ['23000', '23505'], true)) {
                throw ValidationException::withMessages(['breeding_number' => 'Opslaan mislukt door een gegevensconflict. Controleer of het kweeknummer al bestaat en laad de pagina opnieuw.']);
            }
            throw $exception;
        }
    }
}

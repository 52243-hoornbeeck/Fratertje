@extends('layouts.app')
@section('title', $member->exists ? 'Lid wijzigen' : 'Lid toevoegen')
@section('content')
<a class="back" href="{{ $member->exists ? route('members.show', $member) : route('members.index') }}">← Terug</a>
<div class="page-heading"><div><p class="eyebrow">Ledenadministratie</p><h1>{{ $member->exists ? 'Lid wijzigen' : 'Lid toevoegen' }}</h1><p class="muted">Vul de persoonsgegevens, het adres en de lidmaatschapsgegevens in.</p></div></div>
<form method="post" action="{{ $member->exists ? route('members.update', $member) : route('members.store') }}" class="stack">
    @csrf @if($member->exists) @method('PUT') @endif
    <section class="card"><h2>Persoonsgegevens</h2><div class="form-grid">
        <label>Voornaam<input name="first_name" value="{{ old('first_name', $member->first_name) }}" maxlength="100" autocomplete="given-name" required></label>
        <label>Achternaam<input name="last_name" value="{{ old('last_name', $member->last_name) }}" maxlength="100" autocomplete="family-name" required></label>
        <label>Geboortedatum<input type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required></label>
    </div></section>
    <section class="card"><h2>Adres</h2><div class="form-grid">
        <label>Straat<input name="street" value="{{ old('street', $member->address?->street) }}" maxlength="150" required></label>
        <label>Huisnummer en toevoeging<input name="house_number" value="{{ old('house_number', $member->address?->house_number) }}" maxlength="20" required></label>
        <label>Postcode<input name="postal_code" value="{{ old('postal_code', $member->address?->postal_code) }}" maxlength="20" autocomplete="postal-code" required></label>
        <label>Woonplaats<input name="city" value="{{ old('city', $member->address?->city) }}" maxlength="100" autocomplete="address-level2" required></label>
    </div></section>
    <section class="card"><h2>Lidmaatschap</h2><div class="form-grid">
        <label>Lidsoort<select name="member_type_id" required><option value="">Kies een lidsoort</option>@foreach($types as $type)<option value="{{ $type->id }}" @selected(old('member_type_id', $member->member_type_id) == $type->id)>{{ $type->name }}</option>@endforeach</select></label>
        <label>Status<select name="is_active"><option value="1" @selected((string) old('is_active', (int) $member->is_active) === '1')>Actief</option><option value="0" @selected((string) old('is_active', (int) $member->is_active) === '0')>Inactief</option></select></label>
        <label>Kweeknummer (NBvV)<input name="breeding_number" value="{{ old('breeding_number', $member->breedingNumber?->breeding_number) }}" maxlength="30" aria-describedby="number-help"></label>
        <label>Uitgiftejaar<input name="issue_year" type="number" min="1900" max="{{ now()->year }}" value="{{ old('issue_year', $member->breedingNumber?->issue_year) }}" aria-describedby="number-help"></label>
    </div><p id="number-help" class="hint">Voor jeugd- en volwassen leden zijn het kweeknummer en uitgiftejaar verplicht. Gebruik letters, cijfers of een streepje. Voor gastleden laat je beide velden leeg.</p>
    @if($member->exists)<p class="hint">Bij omzetting naar gastlid wordt het bestaande kweeknummer gearchiveerd. Het nummer blijft gereserveerd.</p>@endif
    </section>
    <div class="actions"><button type="submit">{{ $member->exists ? 'Wijzigingen opslaan' : 'Lid toevoegen' }}</button><a href="{{ route('members.index') }}">Annuleren</a></div>
</form>
@endsection

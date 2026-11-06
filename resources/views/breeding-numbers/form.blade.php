@extends('layouts.app')
@section('title',$number->exists ? 'Kweeknummer wijzigen' : 'Kweeknummer toevoegen')
@section('content')
<a class="back" href="{{ route('breeding-numbers.index') }}">Naar kweeknummers</a><h1>{{ $number->exists ? 'Kweeknummer wijzigen' : 'Kweeknummer toevoegen' }}</h1>
<form method="post" action="{{ $number->exists ? route('breeding-numbers.update',$number) : route('breeding-numbers.store') }}" class="card stack narrow">@csrf @if($number->exists) @method('PUT') @endif
@if($number->exists)
<p>Lid: <strong>{{ $number->member->full_name }}</strong></p><input type="hidden" name="member_id" value="{{ $number->member_id }}">
@else
<label>Lid<select name="member_id" required><option value="">Kies een lid zonder kweeknummer</option>@foreach($members as $member)<option value="{{ $member->id }}" @selected(old('member_id')==$member->id)>{{ $member->full_name }}</option>@endforeach</select></label>
<p class="hint">Alleen leden zonder huidig of gearchiveerd nummer zijn beschikbaar. Een eerder nummer herstel je vanuit het kweeknummerarchief.</p>
@endif
<label>Kweeknummer (NBvV)<input name="breeding_number" value="{{ old('breeding_number',$number->breeding_number) }}" maxlength="30" required></label>
<label>Uitgiftejaar<input name="issue_year" type="number" min="1900" max="{{ now()->year }}" value="{{ old('issue_year',$number->issue_year) }}" required></label>
<p class="hint">Gebruik letters, cijfers of een streepje. Ook gearchiveerde nummers blijven uniek. Het toevoegen van een nummer wijzigt de status van het lid niet.</p>
<button>{{ $number->exists ? 'Wijzigingen opslaan' : 'Kweeknummer toevoegen' }}</button>
</form>
@endsection

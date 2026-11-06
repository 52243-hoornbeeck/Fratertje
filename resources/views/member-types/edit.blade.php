@extends('layouts.app')
@section('title', $type->exists ? 'Lidsoort wijzigen' : 'Lidsoort toevoegen')
@section('content')
<a class="back" href="{{ route('member-types.index') }}">Naar lidsoorten</a><h1>{{ $type->exists ? 'Lidsoort wijzigen' : 'Lidsoort toevoegen' }}</h1>
<form method="post" action="{{ $type->exists ? route('member-types.update',$type) : route('member-types.store') }}" class="card stack narrow">@csrf @if($type->exists) @method('PUT') @endif
<label>Naam<input name="name" value="{{ old('name',$type->name) }}" maxlength="100" required></label>
<label>Omschrijving<textarea name="description" rows="3" maxlength="1000" required>{{ old('description',$type->description) }}</textarea></label>
<label>Jaarprijs in euro, vanaf {{ $type->exists ? now()->year+1 : now()->year }}<input name="price" inputmode="decimal" pattern="[0-9]{1,5}(\.[0-9]{1,2})?" value="{{ old('price',$type->exists ? number_format(($type->rateForYear(now()->year+1)?->amount_cents ?? 0)/100,2,'.','') : '') }}" required></label>
<p class="hint">Gebruik een punt als decimaalteken, bijvoorbeeld 36.00. Bij wijzigen blijven de tarieven voor het lopende jaar en eerdere jaren bewaard. Ieder actief lid moet een kweeknummer hebben.</p>
<button>{{ $type->exists ? 'Wijzigingen opslaan' : 'Lidsoort toevoegen' }}</button>
</form>
@endsection

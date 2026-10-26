@extends('layouts.app')
@section('title', 'Lidsoort wijzigen')
@section('content')
<a class="back" href="{{ route('member-types.index') }}">← Naar lidsoorten</a><h1>Lidsoort wijzigen</h1>
<form method="post" action="{{ route('member-types.update', $type) }}" class="card stack narrow">@csrf @method('PUT')
    <label>Naam<input name="name" value="{{ old('name', $type->name) }}" maxlength="100" required></label>
    <label>Omschrijving<textarea name="description" rows="3" maxlength="1000" required>{{ old('description', $type->description) }}</textarea></label>
    <label>Jaarprijs in euro, vanaf {{ now()->year + 1 }}<input name="price" inputmode="decimal" pattern="[0-9]{1,5}(\.[0-9]{1,2})?" value="{{ old('price', number_format(($type->rateForYear(now()->year + 1)?->amount_cents ?? 0) / 100, 2, '.', '')) }}" required></label>
    <p class="hint">Gebruik een punt als decimaalteken, bijvoorbeeld 36.00. Het lopende jaar en eerdere jaartarieven veranderen niet. De leeftijds- en NBvV-regels blijven gekoppeld aan de oorspronkelijke lidsoort.</p>
    <button>Wijzigingen opslaan</button>
</form>
@endsection

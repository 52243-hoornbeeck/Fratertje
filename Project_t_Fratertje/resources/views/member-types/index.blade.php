@extends('layouts.app')
@section('title', 'Lidsoorten')
@section('content')
<div class="page-heading"><div><p class="eyebrow">Instellingen</p><h1>Lidsoorten en tarieven</h1><p class="muted">Drie vaste lidsoorten. Tariefwijzigingen gaan volgend kalenderjaar in.</p></div></div>
<div class="stats">
@foreach($types as $type)
<section class="card"><p class="eyebrow">{{ $type->code ?? 'Nog niet ingedeeld' }}</p><h2>{{ $type->name }}</h2><p>{{ $type->description }}</p><dl><dt>Jaarprijs {{ now()->year }}</dt><dd class="price">{{ $type->rateForYear(now()->year) ? \App\Support\Money::euros($type->rateForYear(now()->year)->amount_cents) : 'Niet ingesteld' }}</dd><dt>Jaarprijs {{ now()->year + 1 }}</dt><dd>{{ $type->rateForYear(now()->year + 1) ? \App\Support\Money::euros($type->rateForYear(now()->year + 1)->amount_cents) : 'Niet ingesteld' }}</dd></dl><a href="{{ route('member-types.edit', $type) }}">Lidsoort wijzigen →</a></section>
@endforeach
</div>
<p class="hint">Dit zijn jaartarieven. Facturen, jeugdvoordeel en verrekening per maand zijn nog niet beschikbaar in deze basisversie.</p>
@endsection

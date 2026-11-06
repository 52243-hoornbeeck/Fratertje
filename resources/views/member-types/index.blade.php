@extends('layouts.app')
@section('title', request('archive') ? 'Lidsoortenarchief' : 'Lidsoorten')
@section('content')
<div class="page-heading"><div><p class="eyebrow">Lidsoortbeheer</p><h1>{{ request('archive') ? 'Lidsoortenarchief' : 'Lidsoorten en tarieven' }}</h1><p class="muted">Beheer lidsoorten en jaartarieven. Gearchiveerde gegevens blijven bewaard.</p></div><a class="button" href="{{ route('member-types.create') }}">+ Lidsoort toevoegen</a></div>
<p><a href="{{ route('member-types.index') }}">Huidige lidsoorten</a> &middot; <a href="{{ route('member-types.index',['archive'=>1]) }}">Lidsoortenarchief</a></p>
<div class="stats">
@forelse($types as $type)
<section class="card"><h2>{{ $type->name }}</h2><p>{{ $type->description }}</p><dl><dt>Jaarprijs {{ now()->year }}</dt><dd class="price">{{ $type->rateForYear(now()->year) ? \App\Support\Money::euros($type->rateForYear(now()->year)->amount_cents) : 'Niet ingesteld' }}</dd><dt>Jaarprijs {{ now()->year+1 }}</dt><dd>{{ $type->rateForYear(now()->year+1) ? \App\Support\Money::euros($type->rateForYear(now()->year+1)->amount_cents) : 'Niet ingesteld' }}</dd></dl>
@if($type->trashed())
<form method="post" action="{{ route('member-types.restore',$type->id) }}">@csrf<button>Terugzetten</button></form>
@else
<div class="actions"><a href="{{ route('member-types.edit',$type) }}">Lidsoort wijzigen</a><form method="post" action="{{ route('member-types.destroy',$type) }}" data-confirm="Weet je zeker dat je deze lidsoort wilt archiveren? Tarieven blijven bewaard.">@csrf @method('DELETE')<button class="danger">Lidsoort archiveren</button></form></div>
@endif
</section>
@empty<p class="muted">Geen lidsoorten gevonden.</p>@endforelse
</div>
<p class="hint">Een lidsoort met gekoppelde leden, ook in het ledenarchief, kan pas worden gearchiveerd nadat die leden een andere lidsoort hebben gekregen.</p>
@endsection

@extends('layouts.app')
@section('title', $member->full_name)
@section('content')
<a class="back" href="{{ route('members.index', $member->trashed() ? ['archive' => 1] : []) }}">← Naar ledenoverzicht</a>
<div class="page-heading"><div><p class="eyebrow">Lid #{{ $member->id }}</p><h1>{{ $member->full_name }}</h1><span @class(['badge', 'active' => $member->is_active && !$member->trashed()])>{{ $member->trashed() ? 'Gearchiveerd' : ($member->is_active ? 'Actief' : 'Inactief') }}</span></div>@unless($member->trashed())<a class="button" href="{{ route('members.edit', $member) }}">Gegevens wijzigen</a>@endunless</div>
<div class="columns">
<section class="card"><h2>Persoonsgegevens</h2><dl><dt>Naam</dt><dd>{{ $member->full_name }}</dd><dt>Geboortedatum</dt><dd>{{ $member->birth_date->format('d-m-Y') }} ({{ $member->birth_date->age }} jaar)</dd><dt>Adres</dt><dd>{{ $member->address->street }} {{ $member->address->house_number }}<br>{{ $member->address->postal_code }} {{ $member->address->city }}</dd></dl></section>
<section class="card"><h2>Lidmaatschap</h2><dl><dt>Lidsoort</dt><dd>{{ $member->memberType->name }}</dd><dt>NBvV-kweeknummer</dt><dd>{{ $member->breedingNumber?->breeding_number ?? 'Geen' }}</dd><dt>Uitgiftejaar</dt><dd>{{ $member->breedingNumber?->issue_year ?? '—' }}</dd>@if($oldNumber)<dt>Gearchiveerd kweeknummer</dt><dd>{{ $oldNumber->breeding_number }} ({{ $oldNumber->issue_year }})</dd>@endif<dt>Toegevoegd op</dt><dd>{{ $member->created_at->format('d-m-Y') }}</dd></dl></section>
</div>
<section class="card lifecycle"><h2>{{ $member->trashed() ? 'Lid terugzetten' : 'Lid archiveren' }}</h2>
@if($member->trashed())
<p>Dit lid staat sinds {{ $member->deleted_at->format('d-m-Y') }} in het archief. Terugzetten herstelt de eerdere status.</p><form method="post" action="{{ route('members.restore', $member->id) }}">@csrf<button>Terugzetten uit archief</button></form>
@else
<p>Bij archiveren blijven de persoonsgegevens, het adres en het kweeknummer bewaard.</p><form method="post" action="{{ route('members.destroy', $member) }}" data-confirm="Weet je zeker dat je dit lid wilt archiveren?">@csrf @method('DELETE')<button class="danger">Lid archiveren</button></form>
@endif
</section>
@endsection

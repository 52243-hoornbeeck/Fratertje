@extends('layouts.app')
@section('title', request('archive') ? 'Archief' : 'Leden')
@section('content')
<div class="page-heading"><div><p class="eyebrow">Ledenadministratie</p><h1>{{ request('archive') ? 'Archief' : 'Leden' }} <span class="count">{{ $members->total() }}</span></h1><p class="muted">{{ request('archive') ? 'Bekijk of herstel gearchiveerde leden.' : 'Zoek, bekijk en beheer de leden van de vereniging.' }}</p></div><a class="button" href="{{ route('members.create') }}">+ Lid toevoegen</a></div>
<section class="card">
<form method="get" class="filters" action="{{ route('members.index') }}">
    @if(request('archive'))<input type="hidden" name="archive" value="1">@endif
    <label>Zoeken<input type="search" name="q" value="{{ request('q') }}" placeholder="Naam of kweeknummer" maxlength="100"></label>
    <label>Lidsoort<select name="member_type_id"><option value="">Alle lidsoorten</option>@foreach($types as $type)<option value="{{ $type->id }}" @selected(request('member_type_id') == $type->id)>{{ $type->name }}</option>@endforeach</select></label>
    <label>Status<select name="status"><option value="">Alle statussen</option><option value="active" @selected(request('status') === 'active')>Actief</option><option value="inactive" @selected(request('status') === 'inactive')>Inactief</option></select></label>
    <button type="submit">Filteren</button><a href="{{ route('members.index', request('archive') ? ['archive' => 1] : []) }}">Wissen</a>
</form>
<div class="table-wrap"><table><thead><tr><th>Naam</th><th>Lidsoort</th><th>Woonplaats</th><th>Kweeknummer</th><th>Status</th><th>Actie</th></tr></thead><tbody>
@forelse($members as $member)
<tr><td><a class="name" href="{{ route('members.show', $member) }}">{{ $member->full_name }}</a></td><td>{{ $member->memberType->name }}</td><td>{{ $member->address->city }}</td><td>{{ $member->breedingNumber?->breeding_number ?? '—' }}</td><td><span @class(['badge', 'active' => $member->is_active && !$member->trashed()])>{{ $member->trashed() ? 'Archief' : ($member->is_active ? 'Actief' : 'Inactief') }}</span></td><td><a href="{{ route('members.show', $member) }}">Bekijken →</a></td></tr>
@empty<tr><td colspan="6" class="empty">Geen leden gevonden. Pas de filters aan of voeg een lid toe.</td></tr>@endforelse
</tbody></table></div>
{{ $members->links('partials.pagination') }}
</section>
@endsection

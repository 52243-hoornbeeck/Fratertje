@extends('layouts.app')
@section('title',request('archive') ? 'Kweeknummerarchief' : 'Kweeknummers')
@section('content')
<div class="page-heading"><div><p class="eyebrow">NBvV</p><h1>{{ request('archive') ? 'Kweeknummerarchief' : 'Kweeknummers' }}</h1><p class="muted">Beheer de unieke kweeknummers en hun koppeling met een lid.</p></div><a class="button" href="{{ route('breeding-numbers.create') }}">+ Kweeknummer toevoegen</a></div>
<p><a href="{{ route('breeding-numbers.index') }}">Huidige kweeknummers</a> &middot; <a href="{{ route('breeding-numbers.index',['archive'=>1]) }}">Kweeknummerarchief</a></p>
<section class="card"><div class="table-wrap"><table><thead><tr><th>Kweeknummer</th><th>Lid</th><th>Uitgiftejaar</th><th>Status</th><th>Actie</th></tr></thead><tbody>
@forelse($numbers as $number)
<tr><td><a class="name" href="{{ route('breeding-numbers.show',$number) }}">{{ $number->breeding_number }}</a></td><td>{{ $number->member->full_name }}</td><td>{{ $number->issue_year }}</td><td>{{ $number->trashed() ? 'Nummer gearchiveerd' : ($number->member->trashed() ? 'Lid gearchiveerd' : ($number->member->is_active ? 'Actief' : 'Lid inactief')) }}</td><td><a href="{{ route('breeding-numbers.show',$number) }}">Bekijken</a></td></tr>
@empty<tr><td colspan="5" class="empty">Geen kweeknummers gevonden.</td></tr>@endforelse
</tbody></table></div>{{ $numbers->links('partials.pagination') }}</section>
<p class="hint">Archiveren bewaart het nummer en zet het lid op inactief. Een gearchiveerd nummer blijft gereserveerd en kan worden hersteld.</p>
@endsection

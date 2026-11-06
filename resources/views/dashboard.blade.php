@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading"><div><p class="eyebrow">Overzicht</p><h1>Goedendag, {{ auth()->user()->name }}</h1><p class="muted">Dit is de ledenadministratie van ’t Fratertje.</p></div><a class="button" href="{{ route('members.create') }}">+ Lid toevoegen</a></div>
<div class="stats">
    <a class="card stat" href="{{ route('members.index') }}"><span>Alle leden</span><strong>{{ $total }}</strong><small>Exclusief het archief</small></a>
    <a class="card stat" href="{{ route('members.index', ['status' => 'active']) }}"><span>Actieve leden</span><strong>{{ $active }}</strong><small>In de administratie</small></a>
    <a class="card stat" href="{{ route('members.index', ['archive' => 1]) }}"><span>Gearchiveerd</span><strong>{{ $archived }}</strong><small>Gegevens blijven bewaard</small></a>
</div>
<div class="columns">
<section class="card"><h2>Recent toegevoegd</h2><div class="rows">@forelse($recent as $member)<a class="row" href="{{ route('members.show', $member) }}"><strong>{{ $member->full_name }}</strong><span>{{ $member->memberType->name }}</span></a>@empty<p class="muted">Er zijn nog geen leden. Voeg het eerste lid toe.</p>@endforelse</div></section>
<section class="card"><h2>Leden per lidsoort</h2>@foreach($types as $type)<div class="row"><span>{{ $type->name }}</span><strong>{{ $type->members_count }}</strong></div>@endforeach</section>
</div>
@endsection

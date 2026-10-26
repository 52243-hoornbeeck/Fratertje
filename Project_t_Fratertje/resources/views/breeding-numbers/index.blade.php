@extends('layouts.app')
@section('title', 'Kweeknummers')
@section('content')
<div class="page-heading"><div><p class="eyebrow">NBvV</p><h1>Kweeknummers</h1><p class="muted">Elk kweeknummer hoort bij één lid. Wijzigen en toevoegen kan via het lidformulier.</p></div><a class="button" href="{{ route('members.index') }}">Naar leden</a></div>
<section class="card"><div class="table-wrap"><table><thead><tr><th>Kweeknummer</th><th>Lid</th><th>Uitgiftejaar</th><th>Status</th><th>Actie</th></tr></thead><tbody>
@forelse($numbers as $number)
<tr><td class="name">{{ $number->breeding_number }}</td><td>{{ $number->member->full_name }}</td><td>{{ $number->issue_year }}</td><td>{{ $number->trashed() ? 'Nummer gearchiveerd' : ($number->member->trashed() ? 'Lid gearchiveerd' : ($number->member->is_active ? 'Actief' : 'Lid inactief')) }}</td><td><a href="{{ route('members.show', $number->member) }}">Lid bekijken →</a></td></tr>
@empty<tr><td colspan="5" class="empty">Nog geen kweeknummers geregistreerd.</td></tr>@endforelse
</tbody></table></div>{{ $numbers->links('partials.pagination') }}</section>
<p class="hint">Een verplicht kweeknummer kan niet los van het lid worden verwijderd. Omzetten naar gastlid archiveert het nummer; archiveren van een lid bewaart de koppeling.</p>
@endsection

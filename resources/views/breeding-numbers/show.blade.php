@extends('layouts.app')
@section('title',$number->breeding_number)
@section('content')
<a class="back" href="{{ route('breeding-numbers.index',$number->trashed() ? ['archive'=>1] : []) }}">Naar kweeknummers</a>
<div class="page-heading"><div><p class="eyebrow">NBvV-kweeknummer</p><h1>{{ $number->breeding_number }}</h1><span class="badge">{{ $number->trashed() ? 'Gearchiveerd' : 'Geregistreerd' }}</span></div>@unless($number->trashed())<a class="button" href="{{ route('breeding-numbers.edit',$number) }}">Kweeknummer wijzigen</a>@endunless</div>
<section class="card"><h2>Gegevens</h2><dl><dt>Kweeknummer</dt><dd>{{ $number->breeding_number }}</dd><dt>Uitgiftejaar</dt><dd>{{ $number->issue_year }}</dd><dt>Lid</dt><dd><a href="{{ route('members.show',$number->member) }}">{{ $number->member->full_name }}</a></dd><dt>Lidstatus</dt><dd>{{ $number->member->trashed() ? 'Gearchiveerd' : ($number->member->is_active ? 'Actief' : 'Inactief') }}</dd></dl></section>
<section class="card lifecycle"><h2>{{ $number->trashed() ? 'Kweeknummer herstellen' : 'Kweeknummer archiveren' }}</h2>
@if($number->trashed())
<p>Het nummer is bewaard en blijft gereserveerd. Herstellen verandert de status van het lid niet.</p><form method="post" action="{{ route('breeding-numbers.restore',$number->id) }}">@csrf<button>Kweeknummer herstellen</button></form>
@else
<p>Bij archiveren blijven de gegevens bewaard. Het lid wordt inactief, zodat er nooit een actief lid zonder kweeknummer ontstaat.</p><form method="post" action="{{ route('breeding-numbers.destroy',$number) }}" data-confirm="Weet je zeker dat je dit kweeknummer wilt archiveren? Het lid wordt inactief. Alle gegevens blijven bewaard.">@csrf @method('DELETE')<button class="danger">Kweeknummer archiveren</button></form>
@endif
</section>
@endsection

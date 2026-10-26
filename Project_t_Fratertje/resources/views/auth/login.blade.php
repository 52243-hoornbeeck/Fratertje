@extends('layouts.app')
@section('title', 'Inloggen')
@section('content')
<section class="login card">
    <p class="eyebrow">Welkom terug</p><h1>Inloggen</h1><p class="muted">Beheer de leden van ’t Fratertje op één plek.</p>
    <form method="post" action="{{ route('login') }}" class="stack">
        @csrf
        <label for="email">E-mailadres<input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" maxlength="255" required autofocus></label>
        <label for="password">Wachtwoord<input id="password" name="password" type="password" autocomplete="current-password" required></label>
        <button type="submit">Inloggen</button>
    </form>
</section>
@endsection

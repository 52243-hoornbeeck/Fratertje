<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ledenadministratie') · ’t Fratertje</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="{{ asset('js/admin.js') }}" defer></script>
</head>
<body>
<a class="skip" href="#main">Naar inhoud</a>
<header>
    <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark" aria-hidden="true">F</span><span>’t Fratertje<small>Vogelvereniging · ledenadministratie</small></span></a>
    @auth
    <nav aria-label="Hoofdnavigatie">
        <a @class(['current' => request()->routeIs('dashboard')]) href="{{ route('dashboard') }}">Dashboard</a>
        <a @class(['current' => request()->routeIs('members.*') && !request('archive')]) href="{{ route('members.index') }}">Leden</a>
        <a @class(['current' => request()->routeIs('member-types.*')]) href="{{ route('member-types.index') }}">Lidsoorten</a>
        <a @class(['current' => request()->routeIs('breeding-numbers.*')]) href="{{ route('breeding-numbers.index') }}">Kweeknummers</a>
        <a href="{{ route('members.index', ['archive' => 1]) }}">Archief</a>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="quiet" type="submit">Uitloggen</button></form>
    </nav>
    @endauth
</header>
<main id="main">
    @if(session('success'))<div class="notice success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())
    <div class="notice error" role="alert"><strong>Controleer de ingevulde gegevens.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
<footer>’t Fratertje <span>Administratie • {{ now()->year }}</span></footer>
</body>
</html>

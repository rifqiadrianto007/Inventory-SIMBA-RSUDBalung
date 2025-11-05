<h2>Dashboard Instalasi</h2>
<p>Halo {{ auth()->user()->name }}!</p>
<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
<form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>

<h2>Dashboard Super Admin</h2>
<p>Halo {{ auth()->user()->name }}, Anda login sebagai Super Admin.</p>

<a href="{{ route('logout') }}"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
   Logout
</a>

<form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>

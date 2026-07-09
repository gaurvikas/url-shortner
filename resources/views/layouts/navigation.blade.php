<nav>
    <a href="{{ route('dashboard') }}">Sembark URL Shortener</a>
    
    <a href="{{ route('dashboard') }}">Dashboard</a>
    
    <span>{{ucfirst(Auth::user()->name)}}</span>
    
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit">Logout</button>
    </form>
</nav>
<hr>

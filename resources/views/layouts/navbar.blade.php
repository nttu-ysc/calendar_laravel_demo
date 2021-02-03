<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        @if (request()->is('Gcalendar'))
        <a class="navbar-brand" href="/Gcalendar">Google Calendar</a>
        @else
        <a class="navbar-brand" href="index.php">Calendar</a>
        @endif
        <div class="collapse navbar-collapse d-flex justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav ">
                @if (request()->is('api/Gcalendar'))
                <li class="nav-item">
                    <a class="nav-link" href="/">Go To Calendar</a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link" href="/api/Gcalendar">Go To Google Calendar</a>
                </li>
                @endif
                @if (Auth::check())
                <li class="nav-item">
                    <a class="nav-link">{{Auth::user()->name}}</a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                this.closest('form').submit();">Log Out</a>
                    </form>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
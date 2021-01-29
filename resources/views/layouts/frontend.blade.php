<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head')
</head>

<body>
    @include('layouts.navbar')
    
    @yield('calendar')

    @yield('info-panel')
</body>

</html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>Calendar</title>

<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/main.css">

<script src="/js/jquery-3.5.1.min.js"></script>
@if (request()->is('api/Gcalendar'))
<script src="/js/gaction.js"></script>
@else
<script src="/js/action.js"></script>
@endif
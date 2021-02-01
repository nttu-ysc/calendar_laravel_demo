@extends('layouts.frontend')

@section('calendar')
<div id="calendar" data-year="<?= date('Y') ?>" data-month="<?= date('m') ?>">
    <div id="header">
        <?= date('Y') ?>/<?= date('m') ?>
    </div>
    <div id="days" class="clearfix">
        <div class="day">SUN</div>
        <div class="day">MON</div>
        <div class="day">TUE</div>
        <div class="day">WED</div>
        <div class="day">THR</div>
        <div class="day">FRI</div>
        <div class="day">SAT</div>
    </div>

    <div id="date" class="clearfix" data-id="" data-from="">
        <?php foreach ($dates as $key => $date) : ?>
        <div class="date-block <?= (is_null($date)) ? 'empty' : '' ?>" data-date="<?= $date ?>">
            <div class="date">
                <?= $date ?>
            </div>
            <div class="events">
                @foreach ($events as $event)
                @if (($event->date == $date) && ($event->month == $month) && ($event->year == $year))
                <div class="event clearfix" data-id="{{$event->id}}" data-from="{{$event->start_time}}">
                    <div class="title">{{$event->title}}</div>
                    <div class="from">{{$event->start_time}}</div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>
@endsection

@section('info-panel')
<div id="info-panel" class="clearfix">
    <div class="close">X</div>
    <form>
        <input type="hidden" name="id">
        <div class="title">
            <label>event</label><br>
            <input type="text" name="title">
        </div>
        <div class="error-msg">
            <div class="alert alert-danger"></div>
        </div>
        <div class="time-picker">
            <div class="selected-date">
                <span class="month"></span>/<span class="date"></span>
                <input type="hidden" name="year">
                <input type="hidden" name="month">
                <input type="hidden" name="date">
            </div>
            <div class="form">
                <label for="from">from</label><br>
                <input id="from" type="time" name="start_time">
            </div>
            <div class="to">
                <label for="to">to</label><br>
                <input id="to" type="time" name="end_time">
            </div>
        </div>
        <div class="description">
            <label>description</label><br>
            <textarea name="description" id="description"></textarea>
        </div>
    </form>
    <div class="buttons clearfix">
        <button class="create">create</button>
        <button class="update">update</button>
        <button class="cancel">cancel</button>
        <button class="delete">delete</button>
    </div>
</div>
@endsection
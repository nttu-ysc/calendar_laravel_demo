<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvent;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = User::find(Auth::id())->events->sortBy('start_time');
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        $day = $now->day;
        $days_in_month = $now->daysInMonth;
        $now->day = '01';
        $frontPadding = $now->dayOfWeek;
        $now->day = $days_in_month;
        $backPadding = 6 - $now->dayOfWeek;
        for ($i = 0; $i < $frontPadding; $i++) {
            $dates[] = null;
        }

        for ($i = 1; $i <= $days_in_month; $i++) {
            $dates[] = $i;
        }

        for ($i = 0; $i < $backPadding; $i++) {
            $dates[] = null;
        }
        return view('index', [
            'events' => $events,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'dates' => $dates,
            'now' => $now,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEvent $request)
    {
        $event = new Event;
        $event->fill($request->all());
        $event->user_id = Auth::id();
        $event->save();
        $event->start_time = substr($event->start_time, 0, 5);
        return (['event' => $event]);
    }

    public function show(Event $event)
    {
        return (['event' => $event]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(StoreEvent $request, Event $event)
    {
        $event->fill($request->all());
        $event->save();
        $event->start_time = substr($event->start_time, 0, 5);
        return (['event' => $event]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return (['event' => $event]);
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Http\Request;

class GcalendarController extends Controller
{
    protected $client;

    function __construct()
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Calendar Test');
        $client->setAuthConfig(base_path('credentials.json'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setAccessType('offline');
        $this->client = $client;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
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

            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';
            $optParams = [
                'orderBy' => 'startTime',
                'singleEvents' => true,
            ];
            $results = $service->events->listEvents($calendarId, $optParams);
            $events = $results->getItems();
            foreach ($events as  $key => $event) {
                if ($event['start']['date']) {
                    $events[$key]['start']['date'] = explode('-', $event['start']['date']);
                }
                if ($event['end']['date']) {
                    $events[$key]['end']['date'] = explode('-', $event['end']['date']);
                }
                if ($event['start']['dateTime']) {
                    $start_time = Carbon::create($event['start']['dateTime']);
                    $events[$key]['start']['dateTime'] = $start_time->format('H:i');
                    $events[$key]['start']['date'] = $start_time->format('Y-m-d');
                    $events[$key]['start']['date'] = explode('-', $event['start']['date']);
                }
                if ($event['end']['dateTime']) {
                    $end_time = Carbon::create($event['end']['dateTime']);
                    $events[$key]['end']['dateTime'] = $end_time->format('H:i');
                    $events[$key]['end']['date'] = $end_time->format('Y-m-d');
                    $events[$key]['end']['date'] = explode('-', $event['end']['date']);
                }
            }
            return view('gCalendar.index', [
                'events' => $events,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'dates' => $dates,
                'now' => $now,
            ]);
            // return $events;
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    public function oauth()
    {
        session_start();
        $rUrl = action([GcalendarController::class, 'oauth']);
        $this->client->setRedirectUri($rUrl);
        if (!isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
            return redirect($filtered_url);
        } else {
            $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
            return redirect()->route('Gcalendar.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        session_start();
        $start_time = Carbon::parse($request->year . '-' . $request->month . '-' . $request->date . ' ' . $request->start_time)->toRfc3339String();
        $end_time = Carbon::parse($request->year . '-' . $request->month . '-' . $request->date . ' ' . $request->end_time)->toRfc3339String();

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);

            $calendarId = 'primary';
            $event = new Google_Service_Calendar_Event([
                'summary' => $request->title,
                'description' => $request->description,
                'start' => [
                    'dateTime' => $start_time,
                ],
                'end' => [
                    'dateTime' => $end_time,
                ],
            ]);
            $result = $service->events->insert($calendarId, $event);
            if (!$result) {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }
            return response()->json(['status' => 'success', 'message' => 'Event Created']);
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        session_start();
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $service = new Google_Service_Calendar($this->client);
            $event = $service->events->get('primary', $id);

            $event->start->dateTime = Carbon::parse($event->start->dateTime)->format('H:i');
            $event->end->dateTime = Carbon::parse($event->end->dateTime)->format('H:i');
            return ['event' => $event];
        } else {
            return redirect()->route('oauthCallback');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

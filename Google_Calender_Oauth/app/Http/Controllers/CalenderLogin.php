<?php

namespace App\Http\Controllers;
Use Auth;
use Socialite;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_EventReminders;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_EventAttendee;


use Illuminate\Http\Request;

class CalenderLogin extends Controller{
  public function redirect(){
      return Socialite::driver('google')
          ->scopes(Google_Service_Calendar::CALENDAR)
          ->with(["access_type" => "offline", "prompt" => "consent select_account"])
          ->redirect();
        }

  public function handleProviderCallback(){
        $user = Socialite::driver('google')->user();
                $google_creds = [
                'access_token' => $user->token,
                'refresh_token' => $user->refreshToken,
                'expires_in' => $user->expiresIn
                  ];
        ##Write Logic For Storing User Info

              }

  public function Serviceclient(){
    $refreshToken = "1//0gKreAXgA4SvFCgYIARAAGBASNwF-L9IrsHgokvaU7Pxdl9F-mvILGMPcZ0gGnH2vaMiJvUJebF9BM48GTYDWyDQlThysiQnxXSE";
    $client_id = "1003331218357-6n4t1dvf7bncbag0go9c18cjp88q1cte.apps.googleusercontent.com";
    $client_secret = "KARxfZNqSrvz-0zyCPpYcOUo";
    $dev_id = "AIzaSyC5IokJyHHjOYlOfVaf5p7_iXdGOhe-aNo";
    $client = new Google_Client();
    $client->setClientId($client_id);
    $client->setDeveloperKey($dev_id);
    $client->setClientSecret($client_secret);
    $client->fetchAccessTokenWithRefreshToken($refreshToken);
    return $client;
    }

  public function GetCalendarEvents(){
    $client = $this->Serviceclient();
    $service = new Google_Service_Calendar($client);

    $calendarId = 'primary';
    $optParams = array(
      'maxResults' => 10,
      'orderBy' => 'startTime',
      'singleEvents' => true,
      'timeMin' => date('c'),
    );

    $results = $service->events->listEvents($calendarId, $optParams);
    $events = $results->getItems();
    var_dump(json_encode($events));
    if (empty($events)) {
    print "No upcoming events found.\n";
  } else {
    print "Upcoming events:<br>";
    foreach ($events as $event) {
        $start = $event->start->dateTime;
        if (empty($start)) {
            $start = $event->start->date;
        }
        printf("%s (%s)\n", $event->getSummary(), $start);
    }
          } }

  public function CreateEventCalendar(){
    ## Input in Request
    $summary = 'Google I/O 2020'; $location = '800 Howard St., San Francisco, CA 94103';
    $description = 'Volla Test'; $hours = 24 * 60; $minute = 10;
    $attendees = array(
      array('email' => 'maurya.adity13@GMAIL.COM'),
      array('email' => 'aditya.12@gmail.com'),
    );

    $client = $this->Serviceclient();
    $calendarId = 'primary';
    $service = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event(array(
      'summary' => $summary,
      'location' => $location,
      'description' => $description,
      'start' => array(
        'dateTime' => '2020-05-28T09:00:00-07:00',
        'timeZone' => 'America/Los_Angeles',
      ),
      'end' => array(
        'dateTime' => '2020-05-28T17:00:00-07:00',
        'timeZone' => 'America/Los_Angeles',
      ),
      'recurrence' => array(
        'RRULE:FREQ=DAILY;COUNT=2'
      ),
      'attendees' => $attendees,
      'reminders' => array(
        'useDefault' => FALSE,
        'overrides' => array(
          array('method' => 'email', 'minutes' => $hours),
          array('method' => 'popup', 'minutes' => $minute),
            ),
          ),
        ));
        $event = $service->events->insert($calendarId, $event);
        printf('Event created: %s\n', $event->htmlLink);
        echo $event->getId();

  }

}


// ANOTHER METHOD
// $event = new Google_Service_Calendar_Event();
// $event->setSummary('Fax BU'); #$summry
// $event->setLocation('Somewhere'); #$location
// $start = new Google_Service_Calendar_EventDateTime();
// $start->setDateTime('2020-06-03T10:00:00.000-07:00');
// $start->setTimeZone('America/Los_Angeles');
// $event->setStart($start);
// $end = new Google_Service_Calendar_EventDateTime();
// $end->setDateTime('2020-06-03T10:25:00.000-07:00');
// $end->setTimeZone('America/Los_Angeles');
// $event->setEnd($end);
// //$event->setRecurrence(array('RRULE:FREQ=WEEKLY;UNTIL=20110701T170000Z'));
// $attendee1 = new Google_Service_Calendar_EventAttendee();
// $attendee1->setEmail('attendeeEmail');
// $attendees = array('maurya.aditya13@gmail.com',); #attendees email
// $event->attendees = $attendees;
//
// $reminderI = new Google_Service_Calendar_EventReminders();
// $reminderI->setMethod('email');
// $reminderI->setMinutes('25');
//
// $reminder = new Google_Service_Calendar_EventReminders();
// $reminder->setUseDefault('false');
// $reminder->setOverrides(array($reminderI));
// $event->setReminders($reminder);

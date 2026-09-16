# FT UGM Google Calendar

PHP client library for the Faculty of Engineering UGM calendar service.

## Requirements

* PHP >= 7.4
* Composer

## Installation

Install via Composer:

```bash
composer require ft-ugm/google-calendar
```

## Usage

Create a `GoogleCalendar` instance with the Calendar API URL and API token:

```php
use FtUgm\GoogleCalendar\GoogleCalendar;

$calendar = new GoogleCalendar([
	'base_url' => 'https://calendar-api.ft.ugm.ac.id',
	'token' => 'GOOGLE_CALENDAR_API_TOKEN',
]);
```

### Create Event

```php
use FtUgm\GoogleCalendar\Event;

$event = new Event([
	'summary' => 'IT Team Discussion',
	'description' => 'Discussion about the development of the correspondence service.',
	'start' => [
		'dateTime' => '2026-09-15T10:00:00+07:00',
		'timeZone' => 'Asia/Jakarta',
	],
	'end' => [
		'dateTime' => '2026-09-15T11:00:00+07:00',
		'timeZone' => 'Asia/Jakarta',
	],
	'location' => 'IT Room',
	'attendees' => [
		[
			'email' => 'echo@gmail.com',
		],
	],
]);

$event = $calendar->createEvent($event);

echo $event->getId();
echo $event->getHtmlLink();
```

The `start` and `end` values can also be provided using `DateTime`:

```php
'start' => new \DateTime(
	'2026-09-15 10:00:00',
	new \DateTimeZone('Asia/Jakarta')
),
'end' => new \DateTime(
	'2026-09-15 11:00:00',
	new \DateTimeZone('Asia/Jakarta')
),
```

When using `DateTime`, the timezone is automatically taken from the `DateTime` instance.

When using the array format, the `dateTime` value should follow the RFC 3339 format. The `timeZone` field is optional. When omitted, the Calendar API uses `Asia/Jakarta` as the default timezone.

The `start` and `end` values support both **timed events** and **all-day events**:

| Event type    | `start` / `end` format | Description                                  |
| ------------- | ---------------------- | -------------------------------------------- |
| Timed event   | `dateTime`             | An event with a specific start and end time. |
| All-day event | `date`                 | An event that covers one or more whole days. |

For a timed event, use `dateTime`:

```php
'start' => [
	'dateTime' => '2026-09-15T10:00:00+07:00',
],
'end' => [
	'dateTime' => '2026-09-15T11:00:00+07:00',
],
```

For an all-day event, use `date`:

```php
'start' => [
	'date' => '2026-09-15',
],
'end' => [
	'date' => '2026-09-16',
],
```

For all-day events, the `end` date is **exclusive**. This means the `end` date is the day after the last day of the event.

For example:

| Event             | `start.date` | `end.date`   |
| ----------------- | ------------ | ------------ |
| September 15 only | `2026-09-15` | `2026-09-16` |
| September 15–16   | `2026-09-15` | `2026-09-17` |
| September 15–17   | `2026-09-15` | `2026-09-18` |

In other words, `start.date` is the first day of the event, while `end.date` is the **exclusive end date** and is not included in the event.

> [!IMPORTANT]
> Each `start` and `end` value must contain either `date` or `dateTime`, but not both.

### Get Event

```php
$event = $calendar->getEvent($eventId);

echo $event->getSummary();
echo $event->getHtmlLink();
```

### Update Event

Replaces the event data with the provided representation.

```php
$event = new Event([
	'summary' => 'IT Team Discussion - Revised',
	'description' => 'Discussion about the development of the e-office service.',
	'start' => [
		'dateTime' => '2026-09-16T13:00:00+07:00',
		'timeZone' => 'Asia/Jakarta',
	],
	'end' => [
		'dateTime' => '2026-09-16T14:00:00+07:00',
		'timeZone' => 'Asia/Jakarta',
	],
	'attendees' => [
		[
			'email' => 'echo@gmail.com',
		],
		[
			'email' => 'nova@gmail.com',
		],
		[
			'email' => 'nr@gmail.com',
		],
	],
]);

$event = $calendar->updateEvent($eventId, $event);
```

### Patch Event

Updates only the fields provided.

```php
$event = new Event([
	'summary' => 'Final Title (For Real This Time)',
]);

$event = $calendar->patchEvent($eventId, $event);
```

### Delete Event

```php
$calendar->deleteEvent($eventId);
```

## Error Handling

API and connection errors throw a `GoogleCalendarException`.

```php
use FtUgm\GoogleCalendar\GoogleCalendarException;

try {
	$event = $calendar->getEvent($eventId);
} catch (GoogleCalendarException $e) {
	echo $e->getMessage();
	echo $e->getCode();
}
```

The exception provides the API response through `getResponse()` when available:

```php
$response = $e->getResponse();
```

## License

MIT

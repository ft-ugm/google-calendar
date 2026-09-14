<?php

namespace FtUgm\GoogleCalendar;

class GoogleCalendar
{
	protected $baseUrl;
	protected $token;

	public function __construct(array $config)
    {
        if (empty($config['base_url'])) {
            throw new \InvalidArgumentException(
                'The "base_url" configuration is required.'
            );
        }

        if (!filter_var($config['base_url'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(
                'The "base_url" configuration must be a valid URL.'
            );
        }

        if (empty($config['token'])) {
            throw new \InvalidArgumentException(
                'The "token" configuration is required.'
            );
        }

        $this->baseUrl = rtrim($config['base_url'], '/');
        $this->token = $config['token'];
    }

	public function createEvent(Event $event)
	{
		$response = $this->request(
            'POST',
            '/api/events',
            $this->getRequestData($event)
        );

        return Event::fromArray($response);
	}

	public function getEvent($eventId)
	{
		$response = $this->request(
			'GET',
			'/api/events/' . rawurlencode($eventId)
		);

        return Event::fromArray($response);
	}

	public function updateEvent($eventId, Event $event)
	{
		$response = $this->request(
			'PUT',
			'/api/events/' . rawurlencode($eventId),
			$this->getRequestData($event)
		);

        return Event::fromArray($response);
	}

	public function patchEvent($eventId, Event $event)
	{
		$response = $this->request(
			'PATCH',
			'/api/events/' . rawurlencode($eventId),
			$this->getRequestData($event)
		);

        return Event::fromArray($response);
	}

	public function deleteEvent($eventId)
	{
		$this->request(
			'DELETE',
			'/api/events/' . rawurlencode($eventId)
		);
	}

    protected function getRequestData(Event $event)
    {
        $data = $event->toArray();

        unset(
            $data['id'],
            $data['htmlLink']
        );

        return array_filter($data, function ($value) {
            return $value !== null;
        });
    }

	protected function request($method, $path, array $data = null)
	{
		$ch = curl_init($this->baseUrl . $path);

		$headers = [
			'Accept: application/json',
			'Authorization: Bearer ' . $this->token,
		];

		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => $headers,
		];

		if ($data !== null) {
			$headers[] = 'Content-Type: application/json';

			$options[CURLOPT_HTTPHEADER] = $headers;
			$options[CURLOPT_POSTFIELDS] = json_encode($data);
		}

		curl_setopt_array($ch, $options);

		$response = curl_exec($ch);

		if ($response === false) {
			$error = curl_error($ch);
			$code = curl_errno($ch);

			curl_close($ch);

			throw new GoogleCalendarException(
				'Unable to communicate with Google Calendar API: ' . $error,
				$code
			);
		}

		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		$result = json_decode($response, true);

		if ($statusCode < 200 || $statusCode >= 300) {
			$message = isset($result['error'])
				? $result['error']
				: 'Google Calendar API request failed.';

			throw new GoogleCalendarException(
				$message,
				$statusCode,
				$result
			);
		}

		return $result;
	}
}
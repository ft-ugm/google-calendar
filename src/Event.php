<?php

namespace FtUgm\GoogleCalendar;

class Event
{
	protected $id;
	protected $summary;
	protected $description;
	protected $start;
	protected $end;
	protected $location;
	protected $attendees;
	protected $htmlLink;

	public function __construct(array $data = [])
	{
		$this->id = $data['id'] ?? null;
		$this->summary = $data['summary'] ?? null;
		$this->description = $data['description'] ?? null;
		$this->start = $data['start'] ?? null;
		$this->end = $data['end'] ?? null;
		$this->location = $data['location'] ?? null;
		$this->attendees = $data['attendees'] ?? null;
		$this->htmlLink = $data['htmlLink'] ?? null;
	}

    public static function fromArray(array $data)
    {
        return new self($data);
    }
    
	public function toArray()
	{
		return [
			'id' => $this->id,
			'summary' => $this->summary,
			'description' => $this->description,
			'start' => $this->formatDateTime($this->start),
			'end' => $this->formatDateTime($this->end),
			'location' => $this->location,
			'attendees' => $this->attendees,
			'htmlLink' => $this->htmlLink,
		];
	}

	protected function formatDateTime($value)
	{
		if ($value instanceof \DateTimeInterface) {
			return [
				'dateTime' => $value->format(\DateTimeInterface::RFC3339),
				'timeZone' => $value->getTimezone()->getName(),
			];
		}

		return $value;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	public function getSummary()
	{
		return $this->summary;
	}

	public function setSummary($summary)
	{
		$this->summary = $summary;

		return $this;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	public function getStart()
	{
		return $this->start;
	}

	public function setStart($start)
	{
		$this->start = $start;

		return $this;
	}

	public function getEnd()
	{
		return $this->end;
	}

	public function setEnd($end)
	{
		$this->end = $end;

		return $this;
	}

	public function getLocation()
	{
		return $this->location;
	}

	public function setLocation($location)
	{
		$this->location = $location;

		return $this;
	}

	public function getAttendees()
	{
		return $this->attendees;
	}

	public function setAttendees(array $attendees)
	{
		$this->attendees = $attendees;

		return $this;
	}

	public function getHtmlLink()
	{
		return $this->htmlLink;
	}

	public function setHtmlLink($htmlLink)
	{
		$this->htmlLink = $htmlLink;

		return $this;
	}
}
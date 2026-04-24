<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: 'sessions')]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $startTime;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $endTime;

    #[ORM\Column(type: 'string', length: 255)]
    private string $room;

    #[ORM\Column(type: 'string', length: 100)]
    private string $track;

    #[ORM\ManyToOne(targetEntity: Conference::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private Conference $conference;

    #[ORM\ManyToMany(targetEntity: Speaker::class, inversedBy: 'sessions')]
    #[ORM\JoinTable(name: 'session_speaker')]
    private Collection $speakers;

    #[ORM\ManyToMany(targetEntity: Attendee::class, inversedBy: 'sessions')]
    #[ORM\JoinTable(name: 'session_attendee')]
    private Collection $attendees;

    #[ORM\Column(type: 'integer')]
    private int $capacity = 100;

    public function __construct()
    {
        $this->speakers = new ArrayCollection();
        $this->attendees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function setRoom(string $room): self
    {
        $this->room = $room;
        return $this;
    }

    public function getTrack(): string
    {
        return $this->track;
    }

    public function setTrack(string $track): self
    {
        $this->track = $track;
        return $this;
    }

    public function getConference(): Conference
    {
        return $this->conference;
    }

    public function setConference(Conference $conference): self
    {
        $this->conference = $conference;
        return $this;
    }

    public function getSpeakers(): Collection
    {
        return $this->speakers;
    }

    public function addSpeaker(Speaker $speaker): self
    {
        if (!$this->speakers->contains($speaker)) {
            $this->speakers->add($speaker);
            $speaker->addSession($this);
        }
        return $this;
    }

    public function removeSpeaker(Speaker $speaker): self
    {
        if ($this->speakers->removeElement($speaker)) {
            $speaker->removeSession($this);
        }
        return $this;
    }

    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    public function addAttendee(Attendee $attendee): self
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
            $attendee->addSession($this);
        }
        return $this;
    }

    public function removeAttendee(Attendee $attendee): self
    {
        if ($this->attendees->removeElement($attendee)) {
            $attendee->removeSession($this);
        }
        return $this;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getAttendeeCount(): int
    {
        return $this->attendees->count();
    }

    public function getAvailableSeats(): int
    {
        return max(0, $this->capacity - $this->getAttendeeCount());
    }
}

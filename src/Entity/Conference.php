<?php

namespace App\Entity;

use App\Repository\ConferenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConferenceRepository::class)]
#[ORM\Table(name: 'conferences')]
class Conference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'date')]
    private \DateTime $startDate;

    #[ORM\Column(type: 'date')]
    private \DateTime $endDate;

    #[ORM\Column(type: 'string', length: 255)]
    private string $location;

    #[ORM\Column(type: 'integer')]
    private int $capacity;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'conference', cascade: ['remove'])]
    private Collection $sessions;

    #[ORM\OneToMany(targetEntity: Speaker::class, mappedBy: 'conference', cascade: ['remove'])]
    private Collection $speakers;

    #[ORM\OneToMany(targetEntity: Attendee::class, mappedBy: 'conference', cascade: ['remove'])]
    private Collection $attendees;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->speakers = new ArrayCollection();
        $this->attendees = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setConference($this);
        }
        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            if ($session->getConference() === $this) {
                $session->setConference(null);
            }
        }
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
            $speaker->setConference($this);
        }
        return $this;
    }

    public function removeSpeaker(Speaker $speaker): self
    {
        if ($this->speakers->removeElement($speaker)) {
            if ($speaker->getConference() === $this) {
                $speaker->setConference(null);
            }
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
            $attendee->setConference($this);
        }
        return $this;
    }

    public function removeAttendee(Attendee $attendee): self
    {
        if ($this->attendees->removeElement($attendee)) {
            if ($attendee->getConference() === $this) {
                $attendee->setConference(null);
            }
        }
        return $this;
    }
}

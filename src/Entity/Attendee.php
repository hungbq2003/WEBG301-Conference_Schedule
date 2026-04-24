<?php

namespace App\Entity;

use App\Repository\AttendeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttendeeRepository::class)]
#[ORM\Table(name: 'attendees')]
class Attendee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $company;

    #[ORM\Column(type: 'string', length: 255)]
    private string $jobTitle;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ticketType;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $registeredAt;

    #[ORM\Column(type: 'boolean')]
    private bool $checkedIn = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $checkedInAt = null;

    #[ORM\ManyToOne(targetEntity: Conference::class, inversedBy: 'attendees')]
    #[ORM\JoinColumn(nullable: false)]
    private Conference $conference;

    #[ORM\ManyToMany(targetEntity: Session::class, mappedBy: 'attendees')]
    private Collection $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->registeredAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getJobTitle(): string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;
        return $this;
    }

    public function getTicketType(): string
    {
        return $this->ticketType;
    }

    public function setTicketType(string $ticketType): self
    {
        $this->ticketType = $ticketType;
        return $this;
    }

    public function getRegisteredAt(): \DateTime
    {
        return $this->registeredAt;
    }

    public function isCheckedIn(): bool
    {
        return $this->checkedIn;
    }

    public function setCheckedIn(bool $checkedIn): self
    {
        $this->checkedIn = $checkedIn;
        if ($checkedIn && !$this->checkedInAt) {
            $this->checkedInAt = new \DateTime();
        }
        return $this;
    }

    public function getCheckedInAt(): ?\DateTime
    {
        return $this->checkedInAt;
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

    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
        }
        return $this;
    }

    public function removeSession(Session $session): self
    {
        $this->sessions->removeElement($session);
        return $this;
    }
}

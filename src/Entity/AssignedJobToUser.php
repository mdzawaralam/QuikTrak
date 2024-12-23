<?php

namespace App\Entity;

use App\Repository\AssignedJobToUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssignedJobToUserRepository::class)]
class AssignedJobToUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $job_id = null;

    #[ORM\Column]
    private ?int $timezone_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $assigned_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $job_deadline = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completed_job_at = null;

    #[ORM\Column(length: 255)]
    private ?string $job_status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getJobId(): ?int
    {
        return $this->job_id;
    }

    public function setJobId(int $job_id): static
    {
        $this->job_id = $job_id;

        return $this;
    }

    public function getTimezoneId(): ?int
    {
        return $this->timezone_id;
    }

    public function setTimezoneId(int $timezone_id): static
    {
        $this->timezone_id = $timezone_id;

        return $this;
    }

    public function getAssignedAt(): ?\DateTimeImmutable
    {
        return $this->assigned_at;
    }

    public function setAssignedAt(\DateTimeImmutable $assigned_at): static
    {
        $this->assigned_at = $assigned_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getJobDeadline(): ?\DateTimeInterface
    {
        return $this->job_deadline;
    }

    public function setJobDeadline(?\DateTimeInterface $job_deadline): static
    {
        $this->job_deadline = $job_deadline;

        return $this;
    }

    public function getCompletedJobAt(): ?\DateTimeImmutable
    {
        return $this->completed_job_at;
    }

    public function setCompletedJobAt(?\DateTimeImmutable $completed_job_at): static
    {
        $this->completed_job_at = $completed_job_at;

        return $this;
    }

    public function getJobStatus(): ?string
    {
        return $this->job_status;
    }

    public function setJobStatus(string $job_status): static
    {
        $this->job_status = $job_status;

        return $this;
    }
}

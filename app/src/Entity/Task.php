<?php

/**
 * Task entity.
 */

namespace App\Entity;

use App\Entity\Enum\TaskStatus;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Task.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'tasks')]
class Task
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Created at.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt;

    /**
     * Updated at.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt;

    /**
     * Title.
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $ManyToOne = '';

    /**
     * Category.
     */
    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[Assert\Type(Category::class)]
    #[Assert\NotBlank]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * Tags.
     *
     * @var ArrayCollection<int, Tag>
     */
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\JoinTable(name: 'tasks_tags')]
    private Collection $tags;

    /**
     * Author.
     *
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $author;
    #[ORM\Column(type: 'integer')]
    #[Assert\Choice(choices: [TaskStatus::STATUS_1, TaskStatus::STATUS_2])]
    #[Assert\NotBlank]
    private int $status;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Type('string')]
    #[Assert\NotBlank(message: 'Please enter a comment')]
    #[Assert\Length(max: 500, maxMessage: 'This value is too long. It should have {{ limit }} characters or less.')]
    private ?string $comment = null;

    /**
     * Task constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->status = TaskStatus::STATUS_1;//formularz dodawania task działa
    }

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for created at.
     *
     * @return \DateTimeImmutable|null Created at
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param \DateTimeImmutable|null $createdAt Created at
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for updated at.
     *
     * @return \DateTimeImmutable|null Updated at
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param \DateTimeImmutable|null $updatedAt Updated at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string|null $title Title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getManyToOne(): string
    {
        return $this->ManyToOne;
    }

    /**
     * @param string $ManyToOne
     */
    public function setManyToOne(string $ManyToOne): void
    {
        $this->ManyToOne = $ManyToOne;
    }

    /**
     * Getter for category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     *
     * @return $this
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        if (!in_array($status, [TaskStatus::STATUS_1, TaskStatus::STATUS_2])) {
            throw new InvalidArgumentException('Invalid task status');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return $this
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: false, options: ["default" => "Dostępne"])]
    private ?string $reservationStatus = 'Dostępne';

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $reservedBy;

    // ...

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public function getReservationStatus(): ?string
    {
        return $this->reservationStatus;
    }

    /**
     * @param string|null $reservationStatus
     *
     * @return $this
     */
    public function setReservationStatus(?string $reservationStatus): self
    {
        $this->reservationStatus = $reservationStatus;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getReservedBy(): ?User
    {
        return $this->reservedBy;
    }

    /**
     * @param User|null $reservedBy
     *
     * @return $this
     */
    public function setReservedBy(?User $reservedBy): self
    {
        $this->reservedBy = $reservedBy;

        return $this;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $reservedByEmail;

    /**
     * @return string|null
     */
    public function getReservedByEmail(): ?string
    {
        return $this->reservedByEmail;
    }

    /**
     * @param string|null $reservedByEmail
     *
     * @return $this
     */
    public function setReservedByEmail(?string $reservedByEmail): self
    {
        $this->reservedByEmail = $reservedByEmail;

        return $this;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nickname = null;

    // ...

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string|null $nickname
     *
     * @return $this
     */
    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\NotBlank
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reservationComment = null;

    // ...

    /**
     * @return string|null
     */
    public function getReservationComment(): ?string
    {
        return $this->reservationComment;
    }

    /**
     * @param string|null $reservationComment
     *
     * @return $this
     */
    public function setReservationComment(?string $reservationComment): self
    {
        $this->reservationComment = $reservationComment;

        return $this;
    }
}

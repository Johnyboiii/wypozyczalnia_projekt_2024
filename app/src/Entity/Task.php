<?php

/**
 * Task entity.
 */

namespace App\Entity;

use App\Entity\Enum\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
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
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt;

    /**
     * Updated at.
     *
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
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
    private ?string $manyToOne = '';

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
        $this->status = TaskStatus::STATUS_1; // formularz dodawania task działa
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
     * Getter for ManyToOne.
     *
     * @return string ManyToOne
     */
    public function getManyToOne(): string
    {
        return $this->manyToOne;
    }

    /**
     * Setter for ManyToOne.
     *
     * @param string $manyToOne ManyToOne
     */
    public function setManyToOne(string $manyToOne): void
    {
        $this->manyToOne = $manyToOne;
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
     * Add a tag.
     *
     * @param Tag $tag The tag to add
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
     * Remove a tag.
     *
     * @param Tag $tag The tag to remove
     *
     * @return $this
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Getter for author.
     *
     * @return User|null Author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param User|null $author Author
     *
     * @return $this
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Getter for status.
     *
     * @return int Status
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Setter for status.
     *
     * @param int $status Status
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setStatus(int $status): self
    {
        if (!in_array($status, [TaskStatus::STATUS_1, TaskStatus::STATUS_2])) {
            throw new \InvalidArgumentException('Invalid task status');
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Getter for comment.
     *
     * @return string|null Comment
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Setter for comment.
     *
     * @param string|null $comment Comment
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
    #[ORM\Column(type: 'string', length: 255, nullable: false, options: ['default' => 'Dostępne'])]
    private ?string $reservationStatus = 'Dostępne';

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $reservedBy;

    // ...

    /**
     * Getter for reservation status.
     *
     * @return string|null Reservation status
     */
    public function getReservationStatus(): ?string
    {
        return $this->reservationStatus;
    }

    /**
     * Setter for reservation status.
     *
     * @param string|null $reservationStatus Reservation status
     *
     * @return $this
     */
    public function setReservationStatus(?string $reservationStatus): self
    {
        $this->reservationStatus = $reservationStatus;

        return $this;
    }

    /**
     * Getter for reserved by.
     *
     * @return User|null Reserved by
     */
    public function getReservedBy(): ?User
    {
        return $this->reservedBy;
    }

    /**
     * Setter for reserved by.
     *
     * @param User|null $reservedBy Reserved by
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
     * Getter for reserved by email.
     *
     * @return string|null Reserved by email
     */
    public function getReservedByEmail(): ?string
    {
        return $this->reservedByEmail;
    }

    /**
     * Setter for reserved by email.
     *
     * @param string|null $reservedByEmail Reserved by email
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
     * Getter for nickname.
     *
     * @return string|null Nickname
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * Setter for nickname.
     *
     * @param string|null $nickname Nickname
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
     * Getter for reservation comment.
     *
     * @return string|null Reservation comment
     */
    public function getReservationComment(): ?string
    {
        return $this->reservationComment;
    }

    /**
     * Setter for reservation comment.
     *
     * @param string|null $reservationComment Reservation comment
     *
     * @return $this
     */
    public function setReservationComment(?string $reservationComment): self
    {
        $this->reservationComment = $reservationComment;

        return $this;
    }
}

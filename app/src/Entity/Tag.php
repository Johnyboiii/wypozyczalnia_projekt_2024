<?php

/**
 * Tag entity.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @ORM\Table(name="tags")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @UniqueEntity(fields={"title"})
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
#[ORM\Table(name: 'tags')]
#[UniqueEntity(fields: ['title'])]
class Tag
{
    /**
     * @var int|null
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer")
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\Type(\DateTimeImmutable::class)
     *
     * @Gedmo\Timestampable(on="create")
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\Type(\DateTimeImmutable::class)
     *
     * @Gedmo\Timestampable(on="update")
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private \DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @Assert\Type("string")
     *
     * @Assert\Length(min=3, max=64)
     *
     * @Gedmo\Slug(fields={"title"})
     */
    #[ORM\Column(type: 'string', length: 64)]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 64)]
    #[Gedmo\Slug(fields: ['title'])]
    private string $slug;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @Assert\Type("string")
     *
     * @Assert\NotBlank
     *
     * @Assert\Length(min=3, max=64)
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 64)]
    private string $title;

    /**
     * Get the value of id.
     *
     * @return int|null Return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of createdAt.
     *
     * @return \DateTimeInterface|null Return DateTime
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt.
     *
     * @param \DateTimeInterface $createdAt The creation timestamp
     *
     * @return self Return Self
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of updatedAt.
     *
     * @return \DateTimeInterface|null Return DateTime
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt.
     *
     * @param \DateTimeInterface $updatedAt The update timestamp
     *
     * @return self Return Self
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of slug.
     *
     * @return string|null Return String
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set the value of slug.
     *
     * @param string $slug The slug value
     *
     * @return self Return Self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of title.
     *
     * @return string|null Return String
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title.
     *
     * @param string $title The title value
     *
     * @return self Return Self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Updates timestamps before persisting or updating the entity.
     *
     * @ORM\PrePersist
     *
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void
    {
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
        $this->setUpdatedAt(new \DateTimeImmutable());

        // Generate slug based on title
        $this->setSlug($this->generateSlug($this->getTitle()));
    }

    /**
     * Generate slug from title.
     *
     * @param string $title The title value
     *
     * @return string The generated slug
     */
    private function generateSlug(string $title): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }
}

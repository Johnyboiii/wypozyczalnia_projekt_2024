<?php

/**
 * TagService.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class TagService.
 */
class TagService implements TagServiceInterface
{
    private EntityManagerInterface $entityManager;

    private TagRepository $tagRepository;

    /**
     * TagService constructor.
     *
     * @param EntityManagerInterface $entityManager Entity manager
     * @param TagRepository          $tagRepository Tag repository
     */
    public function __construct(EntityManagerInterface $entityManager, TagRepository $tagRepository)
    {
        $this->entityManager = $entityManager;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Create a new tag.
     *
     * @param Tag $tag The tag entity
     *
     * @return Tag The created tag
     */
    public function createTag(Tag $tag): Tag
    {
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return $tag;
    }

    /**
     * Update an existing tag.
     *
     * @param Tag $tag The tag entity
     *
     * @return Tag The updated tag
     */
    public function updateTag(Tag $tag): Tag
    {
        $this->entityManager->flush();

        return $tag;
    }

    /**
     * Delete a tag.
     *
     * @param Tag $tag The tag entity
     */
    public function deleteTag(Tag $tag): void
    {
        $this->entityManager->remove($tag);
        $this->entityManager->flush();
    }

    /**
     * Get a tag by its ID.
     *
     * @param int $id The ID of the tag
     *
     * @return Tag|null The tag entity or null if not found
     */
    public function getTagById(int $id): ?Tag
    {
        return $this->tagRepository->find($id);
    }

    /**
     * Get all tags.
     *
     * @return Tag[] An array of tag entities
     */
    public function getAllTags(): array
    {
        return $this->tagRepository->findAll();
    }

    /**
     * Find by title.
     *
     * @param string $title Tag title
     *
     * @return Tag|null Tag entity
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->tagRepository->findOneByTitle($title);
    }

    /**
     * Save a tag.
     *
     * @param Tag $tag Tag Param
     *
     * @return Tag Return Tag
     */
    public function save(Tag $tag): Tag
    {
        if ($tag->getId()) {
            return $this->updateTag($tag);
        }

        return $this->createTag($tag);
    }

    /**
     * Find by id.
     *
     * @param int $id Tag id
     *
     * @return Tag|null Tag entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->tagRepository->findOneById($id);
    }
}

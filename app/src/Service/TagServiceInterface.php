<?php

/**
 * TagServiceInterface.
 */

namespace App\Service;

use App\Entity\Tag;

/**
 * Interface TagServiceInterface.
 */
interface TagServiceInterface
{
    /**
     * Create a new tag.
     *
     * @param  Tag $tag The tag entity
     *
     * @return Tag      The created tag
     */
    public function createTag(Tag $tag): Tag;

    /**
     * Update an existing tag.
     *
     * @param  Tag $tag The tag entity
     *
     * @return Tag      The updated tag
     */
    public function updateTag(Tag $tag): Tag;

    /**
     * Delete a tag.
     *
     * @param Tag $tag The tag entity
     */
    public function deleteTag(Tag $tag): void;

    /**
     * Get a tag by its ID.
     *
     * @param  int $id  The ID of the tag
     *
     * @return Tag|null The tag entity or null if not found
     */
    public function getTagById(int $id): ?Tag;

    /**
     * Get all tags.
     *
     * @return Tag[] An array of tag entities
     */
    public function getAllTags(): array;

    /**
     * Find by title.
     *
     * @param string $title Tag title
     *
     * @return Tag|null Tag entity
     */
    public function findOneByTitle(string $title): ?Tag;

    /**
     * Find tags by title.
     *
     * @return Tag[]
     */
    public function save(Tag $tag);

    /**
     * Find by id.
     *
     * @param int $id Tag id
     *
     * @return Tag|null Tag entity
     */
    public function findOneById(int $id): ?Tag;
}

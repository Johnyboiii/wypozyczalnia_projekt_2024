<?php

namespace App\Entity\Enum;

use InvalidArgumentException;

/**
 * TaskStatus class.
 */
class TaskStatus
{
    const STATUS_1 = 1;
    const STATUS_2 = 2;

    private int $status;

    /**
     * TaskStatus constructor.
     *
     * @param int $status
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $status)
    {
        if (!in_array($status, [self::STATUS_1, self::STATUS_2])) {
            throw new InvalidArgumentException('Invalid task status');
        }

        $this->status = $status;
    }

    /**
     * Create a TaskStatus from an integer status.
     *
     * @param int $status
     *
     * @return int
     */
    public static function from(int $status): int //dla TaskFixtures, bez tego nie dziala
    {
        if (!in_array($status, [self::STATUS_1, self::STATUS_2])) {
            throw new InvalidArgumentException('Invalid task status');
        }

        return $status;
    }

    /**
     * Get the status.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}

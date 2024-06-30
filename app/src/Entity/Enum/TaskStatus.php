<?php

/**
 * TaskStatus.
 */

namespace App\Entity\Enum;

use InvalidArgumentException;

/**
 * TaskStatus class.
 */
class TaskStatus
{
    public const STATUS_1 = 1;
    public const STATUS_2 = 2;

    private int $status;

    /**
     * TaskStatus constructor.
     *
     * @param  int $status              The status value to initialize the TaskStatus object
     *
     * @throws InvalidArgumentException If an invalid status is provided
     */
    public function __construct(int $status)
    {
        if (!in_array($status, [self::STATUS_1, self::STATUS_2])) {
            throw new InvalidArgumentException('Invalid task status');
        }

        $this->status = $status;
    }

    /**
     * Create a TaskStatus object from an integer status.
     *
     * @param  int $status The status value to create the TaskStatus object from
     * @return int         The created TaskStatus object
     */
    public static function from(int $status): int // dla TaskFixtures, bez tego nie dziala
    {
        if (!in_array($status, [self::STATUS_1, self::STATUS_2])) {
            throw new InvalidArgumentException('Invalid task status');
        }

        return $status;
    }

    /**
     * Get the status value.
     *
     * @return int The status value
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}

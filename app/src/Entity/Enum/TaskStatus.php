<?php
// src/Entity/Enum/TaskStatus.php

namespace App\Entity\Enum;

class TaskStatus
{
    const STATUS_1 = 1;
    const STATUS_2 = 2;

    /**
     * @param int $status
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function from(int $status): int
    {
        if (!in_array($status, [self::STATUS_1, self::STATUS_2])) {
            throw new \InvalidArgumentException('Invalid task status');
        }

        return $status;
    }
}
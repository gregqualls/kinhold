<?php

namespace App\Enums;

enum PointTransactionType: string
{
    case TaskCompletion = 'task_completion';
    case TaskReversal = 'task_reversal';
    case Kudos = 'kudos';
    case Deduction = 'deduction';
    case Redemption = 'redemption';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match($this) {
            self::TaskCompletion => 'Task Completion',
            self::TaskReversal => 'Task Reversal',
            self::Kudos => 'Kudos',
            self::Deduction => 'Deduction',
            self::Redemption => 'Redemption',
            self::Adjustment => 'Adjustment',
        };
    }

    public function isPositive(): bool
    {
        return in_array($this, [self::TaskCompletion, self::Kudos, self::Adjustment]);
    }
}

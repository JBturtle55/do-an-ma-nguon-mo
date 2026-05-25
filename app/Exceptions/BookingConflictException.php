<?php

namespace App\Exceptions;

use Illuminate\Support\Collection;
use RuntimeException;

class BookingConflictException extends RuntimeException
{
    public function __construct(
        private readonly Collection $conflicts,
        string $message = 'Thời gian đã được đặt bởi booking khác.'
    ) {
        parent::__construct($message);
    }

    public function getConflicts(): Collection
    {
        return $this->conflicts;
    }
}

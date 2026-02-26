<?php

namespace App\Repositories\Contracts;

interface OrderTrackingRepositoryInterface
{
    public function findByCodeOrPhone(string $keyword);
}

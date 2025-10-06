<?php

namespace App\Services;

use App\Repositories\SalesPointRepository;

class SalesPointService
{
    public function __construct(private SalesPointRepository $salesPointRepository) {}

    public function getBySalesId(int $salesId)
    {
        return $this->salesPointRepository->getBySalesId($salesId);
    }

    public function create(array $data)
    {
        return $this->salesPointRepository->create($data);
    }

    public function getSalesRecap()
    {
        return $this->salesPointRepository->getSalesRecap();
    }
}

<?php

namespace App\Services;

use App\Repositories\SalesRepository;

class SalesService
{
    public function __construct(
        private SalesRepository $salesRepository,
        private FileManagerService $fileManagerService
    ) {}

    public function getAll()
    {
        return $this->salesRepository->all();
    }

    public function create(array $data)
    {
        if (isset($data['photo'])) {
            $data['photo'] = $this->fileManagerService->upload($data['photo'], 'sales');
        }
        return $this->salesRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $sales = $this->salesRepository->find($id);
        
        if (isset($data['photo'])) {
            if ($sales->photo) {
                $this->fileManagerService->delete($sales->photo, 'sales');
            }
            $data['photo'] = $this->fileManagerService->upload($data['photo'], 'sales');
        }
        
        $this->salesRepository->update($sales, $data);
        return $sales;
    }

    public function delete(int $id)
    {
        $sales = $this->salesRepository->find($id);
        if ($sales->photo) {
            $this->fileManagerService->delete($sales->photo, 'sales');
        }
        return $this->salesRepository->delete($sales);
    }
}

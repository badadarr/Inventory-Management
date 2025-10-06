<?php

namespace App\Services;

use App\Enums\Core\SortOrderEnum;
use App\Enums\Customer\CustomerFieldsEnum;
use App\Enums\Customer\CustomerFiltersEnum;
use App\Exceptions\CustomerNotFoundException;
use App\Exceptions\DBCommitException;
use App\Helpers\ArrayHelper;
use App\Helpers\BaseHelper;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $repository,
        private readonly FileManagerService $fileManagerService
    )
    {
    }

    /**
     * @param array $queryParameters
     * @return LengthAwarePaginator
     */
    public function getAll(array $queryParameters): LengthAwarePaginator
    {
        $page = $queryParameters["page"] ?? 1;
        $perPage = BaseHelper::perPage($queryParameters["per_page"] ?? null);

        return $this->repository->getAll(
            page: $page,
            perPage: $perPage,
            filters: ArrayHelper::getFiltersValues($queryParameters, CustomerFiltersEnum::values()),
            fields: $queryParameters["fields"] ?? [],
            expand: $queryParameters["expand"] ?? [],
            sortBy: $queryParameters["sort_by"] ?? CustomerFieldsEnum::CREATED_AT->value,
            sortOrder: $queryParameters["sort_order"] ?? SortOrderEnum::DESC->value,
        );
    }

    /**
     * @param int $id
     * @param array $expands
     * @return Customer|null
     * @throws CustomerNotFoundException
     */
    public function findByIdOrFail(int $id, array $expands = []): ?Customer
    {
        $customer = $this->repository->find([
            CustomerFiltersEnum::ID->value => $id
        ], $expands);

        if (!$customer) {
            throw new CustomerNotFoundException('Customer not found by the given id.');
        }

        return $customer;
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws DBCommitException
     */
    public function create(array $payload): mixed
    {
        $photo = null;
        if (isset($payload['photo'])) {
            $photo = $this->fileManagerService->uploadFile(
                file: $payload['photo'],
                uploadPath: Customer::PHOTO_PATH
            );
        }

        $processPayload = [
            CustomerFieldsEnum::NAME->value    => $payload[CustomerFieldsEnum::NAME->value],
            CustomerFieldsEnum::NAMA_BOX->value => $payload[CustomerFieldsEnum::NAMA_BOX->value] ?? null,
            CustomerFieldsEnum::NAMA_SALES->value => $payload[CustomerFieldsEnum::NAMA_SALES->value] ?? null,
            CustomerFieldsEnum::NAMA_OWNER->value => $payload[CustomerFieldsEnum::NAMA_OWNER->value] ?? null,
            CustomerFieldsEnum::EMAIL->value   => $payload[CustomerFieldsEnum::EMAIL->value],
            CustomerFieldsEnum::PHONE->value   => $payload[CustomerFieldsEnum::PHONE->value],
            CustomerFieldsEnum::ADDRESS->value => $payload[CustomerFieldsEnum::ADDRESS->value],
            CustomerFieldsEnum::BULAN_JOIN->value => $payload[CustomerFieldsEnum::BULAN_JOIN->value] ?? null,
            CustomerFieldsEnum::TAHUN_JOIN->value => $payload[CustomerFieldsEnum::TAHUN_JOIN->value] ?? null,
            CustomerFieldsEnum::STATUS_CUSTOMER->value => $payload[CustomerFieldsEnum::STATUS_CUSTOMER->value] ?? 'new',
            CustomerFieldsEnum::STATUS_KOMISI->value => $payload[CustomerFieldsEnum::STATUS_KOMISI->value] ?? null,
            CustomerFieldsEnum::HARGA_KOMISI_STANDAR->value => $payload[CustomerFieldsEnum::HARGA_KOMISI_STANDAR->value] ?? null,
            CustomerFieldsEnum::HARGA_KOMISI_EKSTRA->value => $payload[CustomerFieldsEnum::HARGA_KOMISI_EKSTRA->value] ?? null,
            CustomerFieldsEnum::PHOTO->value   => $photo,
        ];

        return $this->repository->create($processPayload);
    }

    /**
     * @param int $id
     * @param array $payload
     * @return Customer
     * @throws CustomerNotFoundException
     * @throws Exception
     */
    public function update(int $id, array $payload): Customer
    {
        $customer = $this->findByIdOrFail($id);

        $photo = $customer->getRawOriginal(CustomerFieldsEnum::PHOTO->value);
        if (isset($payload['photo'])) {
            $photo = $this->fileManagerService->uploadFile(
                file: $payload['photo'],
                uploadPath: Customer::PHOTO_PATH,
                deleteFileName: $photo
            );
        }

        $processPayload = [
            CustomerFieldsEnum::NAME->value    => $payload[CustomerFieldsEnum::NAME->value] ?? $customer->name,
            CustomerFieldsEnum::NAMA_BOX->value => $payload[CustomerFieldsEnum::NAMA_BOX->value] ?? $customer->nama_box,
            CustomerFieldsEnum::NAMA_SALES->value => $payload[CustomerFieldsEnum::NAMA_SALES->value] ?? $customer->nama_sales,
            CustomerFieldsEnum::NAMA_OWNER->value => $payload[CustomerFieldsEnum::NAMA_OWNER->value] ?? $customer->nama_owner,
            CustomerFieldsEnum::EMAIL->value   => $payload[CustomerFieldsEnum::EMAIL->value] ?? $customer->email,
            CustomerFieldsEnum::PHONE->value   => $payload[CustomerFieldsEnum::PHONE->value] ?? $customer->phone,
            CustomerFieldsEnum::ADDRESS->value => $payload[CustomerFieldsEnum::ADDRESS->value] ?? $customer->address,
            CustomerFieldsEnum::BULAN_JOIN->value => $payload[CustomerFieldsEnum::BULAN_JOIN->value] ?? $customer->bulan_join,
            CustomerFieldsEnum::TAHUN_JOIN->value => $payload[CustomerFieldsEnum::TAHUN_JOIN->value] ?? $customer->tahun_join,
            CustomerFieldsEnum::STATUS_CUSTOMER->value => $payload[CustomerFieldsEnum::STATUS_CUSTOMER->value] ?? $customer->status_customer,
            CustomerFieldsEnum::STATUS_KOMISI->value => $payload[CustomerFieldsEnum::STATUS_KOMISI->value] ?? $customer->status_komisi,
            CustomerFieldsEnum::HARGA_KOMISI_STANDAR->value => $payload[CustomerFieldsEnum::HARGA_KOMISI_STANDAR->value] ?? $customer->harga_komisi_standar,
            CustomerFieldsEnum::HARGA_KOMISI_EKSTRA->value => $payload[CustomerFieldsEnum::HARGA_KOMISI_EKSTRA->value] ?? $customer->harga_komisi_ekstra,
            CustomerFieldsEnum::PHOTO->value   => $photo,
        ];

        return $this->repository->update($customer, $processPayload);
    }

    /**
     * @param int $id
     * @return bool|null
     * @throws CustomerNotFoundException
     */
    public function delete(int $id): ?bool
    {
        $customer = $this->findByIdOrFail($id);
        $photo = $customer->getRawOriginal(CustomerFieldsEnum::PHOTO->value);
        if ($photo) {
            $this->fileManagerService->delete(
                fileName: $photo,
                path: Customer::PHOTO_PATH,
            );
        }

        return $this->repository->delete($customer);
    }
}

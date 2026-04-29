<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Empresa;

use App\Models\Branch;
use Livewire\Attributes\Locked;
use Livewire\Form;

final class BranchForm extends Form
{
    #[Locked]
    public ?int $company_id = null;

    #[Locked]
    public ?int $branch_id = null;

    public string $companyName = '';

    public string $name = '';

    public string $code = '';

    public ?int $province_id = null;

    public ?int $region_id = null;

    public string $postal_code = '';

    public string $address = '';

    public string $phone = '';

    public string $email = '';

    public string $website = '';

    public ?int $current_status_id = null;

    public bool $is_default = false;

    public mixed $logo = null;

    public function fillFromBranch(int $branchId): void
    {
        $branch = Branch::query()->with('region')->find($branchId);

        if (! $branch) {
            return;
        }

        $this->branch_id = $branch->id;
        $this->company_id = $branch->company_id;
        $this->current_status_id = $branch->current_status_id;
        $this->province_id = $branch->region?->province_id;
        $this->region_id = $branch->region_id;
        $this->name = $branch->name;
        $this->code = $branch->code;
        $this->phone = $branch->phone ?? '';
        $this->email = $branch->email ?? '';
        $this->website = $branch->website ?? '';
        $this->address = $branch->address ?? '';
        $this->postal_code = $branch->postal_code ?? '';
        $this->is_default = (bool) $branch->is_default;
    }
}

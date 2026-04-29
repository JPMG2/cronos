<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Configuracion\Empresa;

use Livewire\Attributes\Locked;
use Livewire\Form;

final class BranchForm extends Form
{
    #[Locked]
    public ?int $company_id = null;

    #[Locked]
    public ?int $branch_id = null;

    public ?int $current_status_id = null;

    public string $companyName;

    public ?int $region_id = null;

    public string $name = '';

    public string $code = '';

    public string $phone = '';

    public string $email = '';

    public string $website = '';

    public string $address = '';

    public string $postal_code = '';

    public bool $is_default = false;

    public mixed $logo = null;
}

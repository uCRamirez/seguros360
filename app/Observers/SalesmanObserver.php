<?php

namespace App\Observers;

use App\Models\Salesman;

class SalesmanObserver
{
    public function saving(Salesman $salesman)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $salesman->company_id = company()->id;
        }
    }
}

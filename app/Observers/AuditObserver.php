<?php

namespace App\Observers;

use App\Models\Audit;

class AuditObserver
{
    public function saving(Audit $audit)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $audit->company_id = company()->id;
        }
    }
}

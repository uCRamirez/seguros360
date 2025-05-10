<?php

namespace App\Observers;

use App\Models\LeadStatus;

class LeadStatusObserver
{
    public function saving(LeadStatus $leadStatus)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $leadStatus->company_id = company()->id;
        }
    }
}

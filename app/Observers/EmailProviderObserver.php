<?php

namespace App\Observers;

use App\Models\EmailProvider;

class EmailProviderObserver
{
    public function saving(EmailProvider $emailProvider)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $emailProvider->company_id = company()->id;
        }
    }
}
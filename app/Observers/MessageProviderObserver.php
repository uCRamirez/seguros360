<?php

namespace App\Observers;

use App\Models\MessageProvider;

class MessageProviderObserver
{
    public function saving(MessageProvider $messageProvider)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $messageProvider->company_id = company()->id;
        }
    }
}
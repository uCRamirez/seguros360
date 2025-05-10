<?php

namespace App\Observers;

use App\Models\MessageTemplate;

class MessageTemplateObserver
{
    public function saving(MessageTemplate $messageTemplate)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $messageTemplate->company_id = company()->id;
        }
    }
}
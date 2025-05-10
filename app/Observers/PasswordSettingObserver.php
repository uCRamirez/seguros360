<?php

namespace App\Observers;

use App\Models\PasswordSetting;

class PasswordSettingObserver
{
    public function saving(PasswordSetting $passwordSetting)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $passwordSetting->company_id = company()->id;
        }
    }
}

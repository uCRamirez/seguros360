<?php

namespace App\Observers;

use App\Models\NotesTypification;

class NotesTypificationObserver
{
    public function saving(NotesTypification $notesTypification)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $notesTypification->company_id = company()->id;
        }
    }
}
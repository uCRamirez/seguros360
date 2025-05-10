<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AuditExport implements FromArray,  WithHeadings
{
    use Exportable;

    private $allAudits;

    public function __construct($allAudits)
    {
        $this->allAudits = $allAudits;
    }

    public function headings(): array
    {
        return [
            'Id',
            'User Type',
            'User',
            'Auditable Type',
            'Event',
            'Auditable Id',
            'Old Value',
            'New Value',
            'Url',
            'Ip Address',
            'User Agent',
            'Tags',
            'Created At',
            'Updated At'
        ];
    }

    public function array(): array
    {
        $data = [];
        $counter = 1;

        
        foreach ($this->allAudits as $allAudit) {
            
            $data[] = [
                'id' =>  $allAudit['id'],
                'user_type' => $allAudit['user_type'],
                'user' => (isset($allAudit['user']) && $allAudit['user'] != null) ? $allAudit['user']['name'] : '-',
                'auditable_type' => $allAudit['auditable_type'],
                'event' =>  $allAudit['event'],
                'auditable_id' => $allAudit['auditable_id'],
                'old_values' => isset($allAudit['old_values']) && $allAudit['old_values'] != null ? $allAudit['old_values'] : '-',
                'new_values' => isset($allAudit['new_values']) && $allAudit['new_values'] != null ? $allAudit['new_values'] : '-',
                'url' => $allAudit['url'],
                'ip_address' => $allAudit['ip_address'],
                'user_agent' => $allAudit['user_agent'],
                'tags' => (isset($allAudit['tags']) && $allAudit['tags'] != null) ? $allAudit['tags'] : "-" ,
                'created_at' => $allAudit['created_at'],
                'updated_at' => $allAudit['updated_at'],
            ];

            $counter++;
        }

        return $data;
    }
}


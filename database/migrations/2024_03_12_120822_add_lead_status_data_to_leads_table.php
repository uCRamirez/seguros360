<?php

use App\Models\Company;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Scopes\CompanyScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $allCompanies = Company::select('id')->get();

        foreach ($allCompanies as $company) {
            // Estados de lead
            $leadStatuses = [
                'Interested',
                'Not Interested',
                'Unreachable',
            ];
            foreach ($leadStatuses as $name) {
                LeadStatus::create([
                    'company_id' => $company->id,
                    'name'       => $name,
                    'type'       => 'lead_status',
                ]);
            }

            // Estados de estado civil
            $maritalStatuses = [
                'Single',
                'Married',
                'Widower',
                'Divorced',
                'Separate',
            ];
            foreach ($maritalStatuses as $name) {
                LeadStatus::create([
                    'company_id' => $company->id,
                    'name'       => $name,
                    'type'       => 'marital_status',
                ]);
            }
        }

        // Migración de las leads existentes
        $allLeads = Lead::select('id', 'company_id', 'lead_status')
            ->withoutGlobalScope(CompanyScope::class)
            ->get();

        foreach ($allLeads as $lead) {
            $map = [
                'interested'     => 'Interested',
                'not_interested' => 'Not Interested',
                'unreachable'    => 'Unreachable',
            ];

            if (isset($map[$lead->lead_status])) {
                $newName = $map[$lead->lead_status];

                $status = LeadStatus::withoutGlobalScope(CompanyScope::class)
                    ->where('company_id', $lead->company_id)
                    ->where('name', $newName)
                    ->where('type', 'lead_status')
                    ->first();

                if ($status) {
                    $lead->lead_status_id = $status->id;
                    $lead->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: eliminar los estados si usas rollback
        //LeadStatus::where('type', 'lead_status')->delete();
        //LeadStatus::where('type', 'marital_status')->delete();
    }
};

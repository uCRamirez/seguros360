<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\LeadStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class LeadStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('lead_status')->delete();

        DB::statement('ALTER TABLE lead_status AUTO_INCREMENT = 1');

        $company = Company::where('is_global', 0)->first();

        $interested = new LeadStatus();
        $interested->company_id = $company->id;
        $interested->name = 'Interested';
        $interested->save();

        $notInterested = new LeadStatus();
        $notInterested->company_id = $company->id;
        $notInterested->name = 'Not Interested';
        $notInterested->save();

        $unreachable = new LeadStatus();
        $unreachable->company_id = $company->id;
        $unreachable->name = 'Unreachable';
        $unreachable->save();

    }
}
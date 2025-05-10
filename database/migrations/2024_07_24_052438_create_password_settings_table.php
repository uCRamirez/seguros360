<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PasswordSetting;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('upper_case')->default(true);
            $table->boolean('lower_case')->default(true);
            $table->boolean('special_character')->default(true);
            $table->boolean('number')->default(true);
            $table->integer('password_length')->default(0);
            $table->integer('password_expiration')->nullable()->default(0);
            $table->integer('login_failed_attempts')->nullable()->default(0);
            $table->integer('last_used_passwords')->nullable()->default(0);
            $table->integer('disable_inactive_user')->nullable()->default(0);
            $table->boolean('enable_audit_log')->nullable()->default(null);
            $table->timestamps();
        });

        $company = Company::where('is_global', 0)->first();

        DB::table('password_settings')->insertGetId([
            'company_id' => $company->id,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_settings');
    }
};

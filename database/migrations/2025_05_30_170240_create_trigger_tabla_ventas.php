<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUpdateUserIdLeadLogsTrigger extends Migration
{
    public function up()
    {
        // 1) Eliminamos el trigger si ya existe
        DB::unprepared('DROP TRIGGER IF EXISTS `update_user_id_lead_logs`;');

        // 2) Creamos el trigger
        DB::unprepared(<<<'SQL'
        CREATE TRIGGER `update_user_id_lead_logs`
        AFTER UPDATE ON `ventas`
        FOR EACH ROW
        BEGIN
            -- Si cambió el user_id, actualizamos lead_logs
            IF OLD.user_id <> NEW.user_id THEN
                UPDATE `lead_logs`
                SET `user_id` = NEW.user_id
                WHERE `id` = NEW.idNota;
            END IF;
        END;
        SQL
        );
    }

    public function down()
    {
        // Eliminamos el trigger al hacer rollback
        DB::unprepared('DROP TRIGGER IF EXISTS `update_user_id_lead_logs`;');
    }
}

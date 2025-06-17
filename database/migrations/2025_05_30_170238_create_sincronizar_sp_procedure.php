<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSincronizarSpProcedure extends Migration
{
    public function up()
    {
        // 1) eliminamos SP si existe
        DB::unprepared('DROP PROCEDURE IF EXISTS `crm_seguros`.`sincronizarInformacionBaseLeads`;');

        // 2) creamos SP con DEFINER y toda la lógica
        DB::unprepared(<<<'SQL'
        CREATE DEFINER=`root`@`localhost` PROCEDURE `crm_seguros`.`sincronizarInformacionBaseLeads`(
            IN p_campaign_id INT,
            IN p_nombreBase VARCHAR(191),
            IN p_myId INT,
            IN p_cantidadRegistros INT.
            IN p_etapa VARCHAR(191)
        )
        BEGIN
            DECLARE v_old_mode VARCHAR(512);

            -- 1) Guardamos el SQL_MODE actual
            SET v_old_mode = @@SESSION.sql_mode;

            -- 2) Quitamos las restricciones de zero-dates y modo estricto
            SET SESSION sql_mode = REPLACE(
                                    REPLACE(
                                    REPLACE(@@SESSION.sql_mode,
                                            'STRICT_TRANS_TABLES',''),
                                            'NO_ZERO_DATE',''),
                                            'NO_ZERO_IN_DATE','');

            -- 3) Inserción en leads_history
            INSERT INTO crm_seguros.leads_history
            (cedula, nombre, apellido1, apellido2,
            fechaNacimiento, tel1, tel2, email, edad,
            nombreBase, agente, created_at, updated_at,campaign_id,company_id)
            SELECT
            la.cedula,
            la.nombre,
            la.apellido1,
            la.apellido2,
            CASE
                WHEN la.fechaNacimiento = '0000-00-00 00:00:00'
                THEN NULL
                ELSE la.fechaNacimiento
            END AS fechaNacimiento,
            la.tel1,
            la.tel2,
            la.email,
            la.edad,
            la.nombreBase,
            la.agente,
            la.created_at,
            la.updated_at,
            la.campaign_id,
            la.company_id 
            FROM crm_seguros.leads_aux AS la;

            -- 4) Inserción en leads (producción), 
            INSERT INTO crm_seguros.leads
            (cedula, nombre, apellido1, apellido2,
            fechaNacimiento, tel1, tel2, email, edad,
            nombreBase, assign_to, created_at, updated_at, campaign_id, company_id)
            SELECT
            la.cedula,
            la.nombre,
            la.apellido1,
            la.apellido2,
            CASE
                WHEN la.fechaNacimiento = '0000-00-00 00:00:00'
                THEN NULL
                ELSE la.fechaNacimiento
            END AS fechaNacimiento,
            la.tel1,
            la.tel2,
            la.email,
            la.edad,
            la.nombreBase,
            u.id AS assign_to,
            la.created_at,
            la.updated_at, 
            la.campaign_id,
            la.company_id
            FROM crm_seguros.leads_aux AS la
            LEFT JOIN crm_seguros.users AS u
            ON u.`user` = la.agente
            ON DUPLICATE KEY UPDATE
            nombre = VALUES(nombre),
            apellido1 = VALUES(apellido1),
            apellido2 = VALUES(apellido2),
            fechaNacimiento = VALUES(fechaNacimiento),
            tel1 = VALUES(tel1),
            tel2 = VALUES(tel2),
            email = VALUES(email),
            edad = VALUES(edad),
            nombreBase = VALUES(nombreBase),
            assign_to = VALUES(assign_to),
            updated_at = CURRENT_TIMESTAMP;
            
            -- 5) Registrar la base en el historico
            INSERT INTO crm_seguros.bases_historico
            (campaign_id, nombreBase, user_id, cantidadRegistros, etapa, estado, created_at, updated_at)
            VALUES(p_campaign_id, p_nombreBase, p_myId, p_cantidadRegistros, p_etapa, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
            
            -- 6) Limpiar los datos de Aux
            TRUNCATE TABLE crm_seguros.leads_aux;

            -- 7) Actualizar el total de leads de la campana
            UPDATE crm_seguros.campaigns
                SET total_leads = total_leads + p_cantidadRegistros
            WHERE id = p_campaign_id; 

            -- 8) Restauramos el modo SQL original
            SET SESSION sql_mode = v_old_mode;
        END
        SQL
        );
    }

    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `crm_seguros`.`sincronizarInformacionBaseLeads`;');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSincronizarSpProcedure extends Migration
{
    public function up()
    {
        // 1) eliminamos SP si existe
        DB::unprepared('DROP PROCEDURE IF EXISTS `sincronizarInformacionBaseLeads`;');

        // 2) creamos SP con DEFINER y toda la lógica
        DB::unprepared(<<<'SQL'
            CREATE DEFINER=`root`@`localhost` PROCEDURE `sincronizarInformacionBaseLeads`(
                IN p_campaign_id INT,
                IN p_nombreBase VARCHAR(191),
                IN p_myId INT,
                IN p_cantidadRegistros INT,      
                IN p_etapa VARCHAR(191)
            )
            BEGIN
                DECLARE v_old_mode VARCHAR(512);
                DECLARE v_prev_etapa  VARCHAR(191);
                DECLARE v_prev_date   DATETIME;
                DECLARE v_etapa_final VARCHAR(191);
                -- DECLARE v_nuevos      INT;

                -- 0) Handler para atrapar cualquier error y deshacer cambios
                DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
                BEGIN
                    -- Deshacer todos los cambios de la transacción activa
                    ROLLBACK;
                    -- El SP continuará (silenciosamente) tras este bloque
                    -- RESIGNAL;
                END;

                START TRANSACTION;
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
                INSERT INTO leads_history
                (
                    cedula,
                    nombre,
                    segundo_nombre,
                    apellido1,
                    apellido2,
                    tel1,
                    tel2,
                    tel3,
                    tel4,
                    tel5,
                    tel6,
                    email,
                    provincia_voto,
                    hijos,
                    estadoCivil,
                    tipo_plan,
                    fechaVencimiento,
                    tarjeta,
                    tipo_tarjeta,
                    emisor,
                    ultimos_digitos,
                    mes_carga,
                    anno_carga,
                    foco_venta,
                    fechaNacimiento,
                    genero,
                    nacionalidad,
                    agente,
                    edad,
                    nombreBase,
                    created_at,
                    updated_at,
                    campaign_id,
                    company_id,
                    created_by,
                    started
                )
                SELECT
                    la.cedula,
                    la.nombre,
                    la.segundo_nombre,
                    la.apellido1,
                    la.apellido2,
                    la.tel1,
                    la.tel2,
                    la.tel3,
                    la.tel4,
                    la.tel5,
                    la.tel6,
                    la.email,
                    la.provincia_voto,
                    la.hijos,
                    la.estadoCivil,
                    la.tipo_plan,
                    la.fechaVencimiento,
                    la.tarjeta,
                    la.tipo_tarjeta,
                    la.emisor,
                    la.ultimos_digitos,
                    la.mes_carga,
                    la.anno_carga,
                    la.foco_venta,
                    CASE
                        WHEN la.fechaNacimiento = '0000-00-00 00:00:00' THEN NULL
                        ELSE la.fechaNacimiento
                    END AS fechaNacimiento,
                    la.genero,
                    la.nacionalidad,
                    la.agente,
                    la.edad,
                    la.nombreBase,
                    la.created_at,
                    la.updated_at,
                    la.campaign_id,
                    la.company_id,
                    la.created_by,
                    la.started
                FROM leads_aux AS la;


                -- 4) Inserción en leads (producción)
                INSERT INTO leads
                (
                    cedula,
                    nombre,
                    segundo_nombre,
                    apellido1,
                    apellido2,
                    tel1,
                    tel2,
                    tel3,
                    tel4,
                    tel5,
                    tel6,
                    email,
                    provincia_voto,
                    hijos,
                    estadoCivil_id,
                    tipo_plan,
                    fechaVencimiento,
                    tarjeta,
                    tipo_tarjeta,
                    emisor,
                    ultimos_digitos,
                    mes_carga,
                    anno_carga,
                    foco_venta,
                    fechaNacimiento,
                    genero,
                    nacionalidad,
                    edad,
                    nombreBase,
                    assign_to,
                    created_at,
                    updated_at,
                    campaign_id,
                    company_id,
                    created_by,
                    started
                )
                SELECT
                    la.cedula,
                    la.nombre,
                    la.segundo_nombre,
                    la.apellido1,
                    la.apellido2,
                    la.tel1,
                    la.tel2,
                    la.tel3,
                    la.tel4,
                    la.tel5,
                    la.tel6,
                    la.email,
                    la.provincia_voto,
                    la.hijos,
                    ls.id  AS estadoCivil_id,
                    la.tipo_plan,
                    la.fechaVencimiento,
                    la.tarjeta,
                    la.tipo_tarjeta,
                    la.emisor,
                    la.ultimos_digitos,
                    la.mes_carga,
                    la.anno_carga,
                    la.foco_venta,
                    CASE
                        WHEN la.fechaNacimiento = '0000-00-00 00:00:00' THEN NULL
                        ELSE la.fechaNacimiento
                    END AS fechaNacimiento,
                    la.genero,
                    la.nacionalidad,
                    la.edad,
                    la.nombreBase,
                    u.id              AS assign_to,
                    la.created_at,
                    la.updated_at,
                    la.campaign_id,
                    la.company_id,
                    la.created_by,
                    la.started
                FROM leads_aux AS la
                LEFT JOIN users AS u
                ON u.`user` = la.agente
                AND u.status = 'enabled'
                LEFT JOIN lead_status AS ls
                    ON ls.name COLLATE utf8mb4_unicode_ci
                    = la.estadoCivil COLLATE utf8mb4_unicode_ci
                AND ls.type       = 'marital_status'
                ON DUPLICATE KEY UPDATE
                    leads.nombre            = VALUES(nombre),
                    leads.segundo_nombre    = VALUES(segundo_nombre),
                    leads.apellido1         = VALUES(apellido1),
                    leads.apellido2         = VALUES(apellido2),
                    leads.tel1              = VALUES(tel1),
                    leads.tel2              = VALUES(tel2),
                    leads.tel3              = VALUES(tel3),
                    leads.tel4              = VALUES(tel4),
                    leads.tel5              = VALUES(tel5),
                    leads.tel6              = VALUES(tel6),
                    leads.email             = VALUES(email),
                    leads.provincia_voto    = VALUES(provincia_voto),
                    leads.hijos             = VALUES(hijos),
                    leads.estadoCivil_id    = VALUES(estadoCivil_id),
                    leads.tipo_plan         = VALUES(tipo_plan),
                    leads.fechaVencimiento  = VALUES(fechaVencimiento),
                    leads.tarjeta           = VALUES(tarjeta),
                    leads.tipo_tarjeta      = VALUES(tipo_tarjeta),
                    leads.emisor            = VALUES(emisor),
                    leads.ultimos_digitos   = VALUES(ultimos_digitos),
                    leads.mes_carga         = VALUES(mes_carga),
                    leads.anno_carga        = VALUES(anno_carga),
                    leads.foco_venta        = VALUES(foco_venta),
                    leads.fechaNacimiento   = VALUES(fechaNacimiento),
                    leads.genero            = VALUES(genero),
                    leads.nacionalidad      = VALUES(nacionalidad),
                    leads.edad              = VALUES(edad),
                    leads.nombreBase        = VALUES(nombreBase),
                    leads.assign_to         = VALUES(assign_to),
                    leads.started           = VALUES(started),
                    leads.etapa = CASE
                                    WHEN leads.etapa = 'Nueva' THEN 'Reproceso'
                                    WHEN leads.etapa = 'Reproceso' AND leads.updated_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH) THEN 'No aplica'
                                    ELSE leads.etapa
                                END,
                    leads.updated_at        = CURRENT_TIMESTAMP,
                    leads.active = CASE WHEN leads.active = 0 THEN 1 ELSE leads.active END;
                
                -- 5) Intentar obtener la última etapa y fecha de creación para esta base
                SELECT bh.etapa, bh.created_at
                INTO v_prev_etapa, v_prev_date
                FROM bases_historico AS bh
                WHERE bh.nombreBase COLLATE utf8mb4_0900_ai_ci = p_nombreBase COLLATE utf8mb4_0900_ai_ci
                AND bh.campaign_id = p_campaign_id
                ORDER BY bh.created_at DESC
                LIMIT 1;

                -- 6) Calcular la etapa final teniendo en cuenta base nueva
                IF v_prev_etapa IS NULL THEN
                    SET v_etapa_final = 'Nueva';
                ELSEIF v_prev_etapa = 'Nueva' THEN
                    SET v_etapa_final = 'Reproceso';
                ELSEIF v_prev_etapa = 'Reproceso'
                    AND v_prev_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH) THEN
                    SET v_etapa_final = 'N/A';
                ELSEIF v_prev_etapa = 'N/A' THEN
                    SET v_etapa_final = 'N/A';
                ELSE
                    SET v_etapa_final = p_etapa;
                END IF;


                -- 7) Registrar la base en el historico
                INSERT INTO bases_historico(campaign_id, nombreBase, user_id, cantidadRegistros, etapa, estado, created_at, updated_at)
                VALUES(p_campaign_id,p_nombreBase,p_myId,p_cantidadRegistros,v_etapa_final,1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);

                /*SELECT 
                    COUNT(*) 
                INTO v_nuevos
                FROM leads_aux AS la
                WHERE la.campaign_id = p_campaign_id
                AND la.nombreBase = ''
                AND l.cedula IS NULL;*/

                -- 9) Actualizar el total de leads de la campana
            /* UPDATE campaigns
                SET total_leads     = total_leads     + v_nuevos,
                    remaining_leads = remaining_leads + v_nuevos,
                    reference_prefix = v_nuevos
                WHERE id = p_campaign_id;*/

                -- 8) Limpiar los datos de Aux
                TRUNCATE TABLE leads_aux;

                COMMIT;
                -- 10) Restauramos el modo SQL original
                SET SESSION sql_mode = v_old_mode;
            END                         
            SQL
        );
    }

    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `sincronizarInformacionBaseLeads`;');
    }
}


/*
 * 
 * 
 * QUERY DEL REPORTE DE GESTIONES PARA SEGUROS 360  
 * 
 * 
*/

SELECT 
    IFNULL(ll.date_time,'') AS date_time,
    IFNULL(l.cedula,'') AS cedula,
    IFNULL(CONCAT(l.nombre,' ',l.segundo_nombre,' ',l.apellido1,' ',l.apellido2),'') AS nombreCompleto,
    IFNULL(c.name,'') AS campaña,
    IFNULL(u.`user`,'') AS `user`,
    IFNULL(nt1.name,'') AS nivel1,
    IFNULL(nt2.name,'') AS nivel2,
    IFNULL(nt3.name,'') AS nivel3,
    IFNULL(nt4.name,'') AS nivel4,
    IFNULL(ll.notes,'') AS comentarios,
    IFNULL(uc.`number`,'') AS call_number,
    IFNULL(uc.guid,'') AS call_guid,
    IFNULL(v.montoTotal,'') AS total,
    IFNULL(l.nombreBase,'') AS base,
    IFNULL(COALESCE(
      STR_TO_DATE(uc.`date`, '%Y-%m-%d %H:%i:%s'),
      STR_TO_DATE(uc.`date`, '%Y-%m-%dT%H:%i:%s'),
      STR_TO_DATE(uc.`date`, '%d/%m/%Y %H:%i:%s'),
      uc.created_at
    ),'') AS call_at
FROM lead_logs ll 
INNER JOIN leads     l  ON ll.lead_id     = l.id 
INNER JOIN campaigns c  ON ll.campaign_id = c.id 
INNER JOIN users     u  ON ll.user_id     = u.id 
LEFT JOIN notes_typifications nt1 ON ll.notes_typification_id_1 = nt1.id 
LEFT JOIN notes_typifications nt2 ON ll.notes_typification_id_2 = nt2.id 
LEFT JOIN notes_typifications nt3 ON ll.notes_typification_id_3 = nt3.id 
LEFT JOIN notes_typifications nt4 ON ll.notes_typification_id_4 = nt4.id 
LEFT JOIN ventas v ON ll.id = v.idNota 
LEFT JOIN uphone_calls uc
  ON uc.id = (
    SELECT uc2.id
    FROM uphone_calls uc2
    WHERE uc2.user_id     = ll.user_id
      AND uc2.lead_id     = ll.lead_id
      AND (
            uc2.campaign_id = ll.campaign_id
         OR (uc2.campaign_id IS NULL AND ll.campaign_id IS NULL)
          )
    ORDER BY ABS(TIMESTAMPDIFF(
               SECOND,
               ll.date_time,
               COALESCE(
                 STR_TO_DATE(uc2.`date`, '%Y-%m-%d %H:%i:%s'),
                 STR_TO_DATE(uc2.`date`, '%Y-%m-%dT%H:%i:%s'),
                 STR_TO_DATE(uc2.`date`, '%d/%m/%Y %H:%i:%s'),
                 uc2.created_at
               )
           ))
    LIMIT 1
  )
WHERE ll.log_type = 'notes'
AND ll.campaign_id IN (1)
AND ll.date_time BETWEEN '$P!{INITIAL_DATE}' AND '$P!{FINAL_DATE}';

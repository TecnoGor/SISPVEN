SELECT * FROM oficinas JOIN envios ON oficinas.oficina_id = envios.oficina_id  WHERE created_at BETWEEN '$fecha->startOfYear();' AND '$fecha->endOfYear();';

SELECT * FROM usuarios JOIN envios ON usuarios.usuario_id = envios.usuario_id WHERE usuario.oficina_id = ?;

SELECT COUNT(*) FROM envios WHERE created_at BETWEEN '$fecha->startOfYear();' AND '$fecha->endOfYear();' AND oficina_id = ? AND usuario_id = ?;

SELECT COUNT(*) FROM envios WHERE tipo_envio = 'nacional' AND created_at BETWEEN '$fecha->startOfMonth();' AND '$fecha->endOfMonth();' AND oficina_id = ? AND usuario_id = ?;

SELECT COUNT(*) FROM envios WHERE tipo_envio = 'internacional' AND created_at BETWEEN '$fecha->startOfWeek();' AND '$fecha->endOfWeek();' AND oficina_id = ? AND usuario_id = ?;

SELECT SUM(monto) FROM envios WHERE created_at BETWEEN '$fecha->startOfYear();' AND '$fecha->endOfYear();' AND oficina_id = ? AND usuario_id = ?;

SELECT SUM(monto) FROM envios WHERE tipo_envio = 'nacional' AND created_at BETWEEN '$fecha->startOfMonth();' AND '$fecha->endOfMonth();' AND oficina_id = ? AND usuario_id = ?;

SELECT SUM(monto) FROM envios WHERE tipo_envio = 'internacional' AND created_at BETWEEN '$fecha->startOfWeek();' AND '$fecha->endOfWeek();' AND oficina_id = ? AND usuario_id = ?;

SELECT COUNT(*) FROM envios WHERE servicio_id = ? AND created_at BETWEEN '$fecha->startOfYear();' AND '$fecha->endOfYear();' AND oficina_id = ? AND usuario_id = ?;

SELECT SUM(monto) FROM envios WHERE servicio_id = ? AND created_at BETWEEN '$fecha->startOfYear();' AND '$fecha->endOfYear();' AND oficina_id = ? AND usuario_id = ?; 
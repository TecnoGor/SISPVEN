<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Recolectas Iposplus (app de clientes)
    |--------------------------------------------------------------------------
    |
    | Los parámetros de tarifa se resuelven POR NOMBRE contra la tabla
    | `parametro` (el seeder base inserta por posición, así que los ids no
    | son confiables entre entornos). Los ids de servicio y tipo de saca
    | dependen del orden de siembra: sobreescribir por env en producción
    | si difieren.
    |
    */

    // Nombre del parámetro con el factor de cambio de la tarifa Iposplus
    // (equivale al Parametro id 6 del seeder base).
    'parametro_iposplus' => env('RECOLECTAS_PARAMETRO_IPOSPLUS', 'IposPlus'),

    // Nombre del parámetro con la tarifa fija del servicio de recolección.
    'parametro_recoleccion' => env('RECOLECTAS_PARAMETRO_RECOLECCION', 'Recoleccion IposPlus'),

    // Nombre del parámetro con la tasa BCV que se congela como tasa_bs.
    'parametro_tasa_bs' => env('RECOLECTAS_PARAMETRO_TASA_BS', 'BCV DOLAR'),

    // Servicio y tipo de saca con los que se crea el envío al procesar
    // la recolecta (mismos valores que usa el módulo Iposplus de taquilla).
    'servicio_iposplus_id' => (int) env('RECOLECTAS_SERVICIO_IPOSPLUS_ID', 10),
    'tipo_saca_iposplus_id' => (int) env('RECOLECTAS_TIPO_SACA_IPOSPLUS_ID', 14),

    // Prefijo del código de solicitud de recolecta (antes de convertirse en envío).
    'prefijo_codigo' => env('RECOLECTAS_PREFIJO_CODIGO', 'REC-'),

    // Vigencia del par de tokens de la API de clientes.
    'token_access_horas' => (int) env('CLIENTES_TOKEN_ACCESS_HORAS', 12),
    'token_refresh_dias' => (int) env('CLIENTES_TOKEN_REFRESH_DIAS', 30),
];

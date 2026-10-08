<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultaPagoMovilBDV
{
    protected $url = "https://bdvconciliacionqa.banvenez.com:444/getMovement/v2";

    public function consultarPagoMovil($data)
    {
        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->withHeaders([
                    'X-API-Key' => config('services.pagoMovilBDV.api_key'),
                    'Content-Type' => 'application/json'
                ])
                ->post($this->url, $data);

            if ($response->successful()) {
                $json = $response->json();

                Log::info('BDV Conciliación respuesta', ['response' => $json, 'request' => $data]);

                // La API del BDV siempre retorna HTTP 200.
                // El verdadero resultado está en $json['code']:
                //   1000 = Pago conciliado exitosamente
                //   1010 = Error / datos no válidos / ya conciliado
                if (isset($json['code']) && $json['code'] == 1000) {
                    return [
                        'status' => 'APROBADO',
                        'message' => $json['message'] ?? 'Pago conciliado exitosamente',
                        'data' => $json['data'] ?? null,
                    ];
                }

                // El BDV retorna code 1010 cuando el pago ya fue conciliado anteriormente.
                // Esto significa que el pago sí fue procesado, solo que ya se validó antes.
                if (isset($json['code']) && $json['code'] == 1010
                    && isset($json['message']) && str_contains($json['message'], 'ya fue conciliado anteriormente')) {
                    return [
                        'status' => 'APROBADO',
                        'message' => 'Pago verificado (conciliado anteriormente)',
                        'data' => $json['data'] ?? null,
                    ];
                }

                return [
                    'error' => true,
                    'message' => $json['message'] ?? 'Pago no conciliado por el banco',
                    'data' => $json,
                ];
            }

            Log::error('BDV Conciliación HTTP error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'error' => true,
                'message' => 'Error HTTP ' . $response->status() . ' del servidor del banco',
            ];

        } catch (\Exception $e) {

            Log::error('BDV Conciliación excepción', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => true,
                'message' => 'El servicio de conexión con el banco no se encuentra disponible. Por favor, intente más tarde.'
            ];

        }
    }
}
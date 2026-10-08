<?php

namespace App\Helpers;

class UserAgentParser
{
    /**
     * Convierte un user-agent crudo en una descripción legible
     * con navegador, versión y sistema operativo.
     *
     * Ejemplos:
     *   "Chrome 120 - Windows 10"
     *   "Firefox 121 - macOS"
     *   "Safari - iOS"
     */
    public static function parse(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Desconocido';
        }

        $navegador = self::detectarNavegador($userAgent);
        $sistema   = self::detectarSistema($userAgent);

        return $navegador . ' - ' . $sistema;
    }

    private static function detectarNavegador(string $ua): string
    {
        // El orden importa: Edge debe ir antes que Chrome, Opera antes que Chrome, etc.
        $patrones = [
            'Edge'    => '/Edg(?:e|A|iOS)?\/([\d\.]+)/i',
            'Opera'   => '/(?:OPR|Opera)\/([\d\.]+)/i',
            'Brave'   => '/Brave\/([\d\.]+)/i',
            'Chrome'  => '/Chrome\/([\d\.]+)/i',
            'Firefox' => '/Firefox\/([\d\.]+)/i',
            'Safari'  => '/Version\/([\d\.]+).*Safari/i',
            'IE'      => '/MSIE ([\d\.]+)|Trident.*rv:([\d\.]+)/i',
        ];

        foreach ($patrones as $nombre => $regex) {
            if (preg_match($regex, $ua, $coincidencias)) {
                $version = $coincidencias[1] ?? $coincidencias[2] ?? '';
                // Tomar solo el major version (ej. "120.0.0.0" -> "120")
                $major = $version ? explode('.', $version)[0] : '';

                return $major ? "{$nombre} {$major}" : $nombre;
            }
        }

        return 'Navegador desconocido';
    }

    private static function detectarSistema(string $ua): string
    {
        // El orden importa: iOS/iPadOS antes que macOS, Android antes que Linux.
        if (preg_match('/Windows NT 10\.0/i', $ua)) {
            // Windows 11 también reporta NT 10.0; no se distinguen vía user-agent estándar.
            return 'Windows 10/11';
        }
        if (preg_match('/Windows NT 6\.3/i', $ua))   return 'Windows 8.1';
        if (preg_match('/Windows NT 6\.2/i', $ua))   return 'Windows 8';
        if (preg_match('/Windows NT 6\.1/i', $ua))   return 'Windows 7';
        if (preg_match('/Windows NT/i', $ua))        return 'Windows';

        if (preg_match('/iPhone|iPad|iPod/i', $ua))  return 'iOS';
        if (preg_match('/Android/i', $ua))           return 'Android';
        if (preg_match('/Mac OS X|Macintosh/i', $ua)) return 'macOS';
        if (preg_match('/Ubuntu/i', $ua))            return 'Ubuntu';
        if (preg_match('/Linux/i', $ua))             return 'Linux';

        return 'Sistema desconocido';
    }
}

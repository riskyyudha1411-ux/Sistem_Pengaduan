<?php

if (!function_exists('send_whatsapp_notification')) {
    /**
     * Mengirim notifikasi WhatsApp menggunakan Fonnte API Gateway
     * 
     * @param string $to Nomor tujuan (e.g. 628xxx atau 08xxx)
     * @param string $message Isi pesan
     * @return array Response dari API gateway
     */
    function send_whatsapp_notification($to, $message)
    {
        // Path settings
        $settingsPath = WRITEPATH . 'wa_settings.json';
        
        $token = '';
        $enabled = false;
        
        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);
            $token = $settings['token'] ?? '';
            $enabled = filter_var($settings['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        // Jika notifikasi WA dinonaktifkan atau token kosong, return status false
        if (!$enabled || empty($token)) {
            return [
                'status'  => false,
                'message' => 'WhatsApp notification is disabled or Token is empty.'
            ];
        }

        // Format nomor target ke internasional (62xxx) jika diawali 08
        $to = trim($to);
        if (strpos($to, '0') === 0) {
            $to = '62' . substr($to, 1);
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $to,
                'message' => $message,
                'countryCode' => '62', // default Indonesia
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return [
                'status'  => false,
                'message' => 'cURL Error: ' . $err
            ];
        }

        $result = json_decode($response, true);
        return [
            'status'  => $result['status'] ?? false,
            'message' => $result['reason'] ?? ($result['status'] ? 'Pesan terkirim' : 'Gagal mengirim pesan via Fonnte API'),
            'raw'     => $result
        ];
    }
}

<?php
namespace App\Models;

use App\Models\Database;

class Counter extends Database
{
    private $response;

    public function viewCounter($link_uuid, $origin, $device)
    {
        date_default_timezone_set('America/Mexico_City');
        $datenow = date('Y-m-d H:i:s');

        $sql = 'INSERT INTO clics (link_uuid, origin, device, date) VALUES (?, ?, ?, ?)';
        $clic = $this->consult($sql, [$link_uuid, $origin, $device, $datenow]);

        if ($clic) {
            $response['status'] = 'OK';
            $response['message'] = 'view ok';
            return $response;
        }

    }

}

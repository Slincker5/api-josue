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

    public function clicTotal($user_uuid)
    {
        $sql = 'SELECT d.user_uuid, COUNT(c.link_uuid) AS total_clics FROM direcciones d LEFT JOIN clics c ON d.link_uuid = c.link_uuid WHERE d.user_uuid = ?';
        $clic = $this->consult($sql, [$user_uuid]);
        $data = $clic->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

}

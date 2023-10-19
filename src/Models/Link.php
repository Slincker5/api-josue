<?php
namespace App\Models;

use App\Models\Database;
use Ramsey\Uuid\UuidFactory;

class Link extends Database
{
    private $response;

    public function addLink($user_uuid, $link_name, $link_short, $link_real)
    {
        if (empty($link_name) || empty($link_short) || empty($link_real)) {
            $response['status'] = 'error';
            $response['message'] = 'Debes completar todos los campos.';
            return $response;
        } else {
            date_default_timezone_set('America/Mexico_City');

            $datenow = date('Y-m-d H:i:s');
            #GENERANDO UN UUID UNICO PARA EL LINK
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $link_uuid = $uuid->toString();
            $sql = 'INSERT INTO direcciones (link_uuid, user_uuid, link_name, link_short, link_real, date) VALUES (?, ?, ?, ?, ?, ?)';
            $add = $this->consult($sql, [$link_uuid, $user_uuid, $link_name, $link_short, $link_real, $datenow]);

            $response['status'] = 'OK';
            $response['message'] = 'Enlace creado con exito';
            return $response;
        }

    }

    public function listLink($user_uuid)
    {
        $sql = 'SELECT * FROM direcciones WHERE user_uuid = ? ORDER BY date DESC';
        $view = $this->consult($sql, [$user_uuid]);
        $data = $view->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }

    public function editLink($link_name, $link_short, $link_real, $link_uuid, $user_uuid)
    {
        if (empty($link_name) || empty($link_short) || empty($link_real)) {
            $response['status'] = 'error';
            $response['message'] = 'Debes completar todos los campos.';
            return $response;
        } else {
            $sql = 'UPDATE direcciones SET link_name = ?, link_short = ?, link_real = ? WHERE link_uuid = ? AND user_uuid = ?';
            $edit = $this->consult($sql, [$link_name, $link_short, $link_real, $link_uuid, $user_uuid]);
            if ($edit) {
                $response['status'] = 'OK';
                $response['message'] = 'Enlace actualizado correctamente.';
                return $response;
            }
        }
    }

    public function removeLink($link_uuid, $user_uuid)
    {
        $sql = 'DELETE FROM direcciones WHERE link_uuid = ? AND user_uuid = ?';
        $edit = $this->consult($sql, [$link_uuid, $user_uuid]);
        if ($edit) {
            $response['status'] = 'OK';
            $response['message'] = 'Enlace eliminado.';
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

    public function viewLink($link_uuid, $date)
    {
        $sql = 'SELECT 
        c.*,
        o.total_origin AS origins,
        d.total_device AS devices
    FROM `clics` c
    LEFT JOIN (
        SELECT origin, COUNT(origin) AS total_origin
        FROM `clics`
        WHERE link_uuid = ? AND DATE(date) = ?
        GROUP BY origin
    ) o ON c.origin = o.origin
    LEFT JOIN (
        SELECT device, COUNT(device) AS total_device
        FROM `clics`
        WHERE link_uuid = ? AND DATE(date) = ?
        GROUP BY device
    ) d ON c.device = d.device
    WHERE c.link_uuid = ? AND DATE(c.date) = ?
    ORDER BY c.date DESC;
    ';
        $clic = $this->consult($sql, [$link_uuid, $date, $link_uuid, $date, $link_uuid, $date]);
        $data = $clic->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

    public function viewCountryLink($link_uuid)
    {
        $sql = 'SELECT origin AS pais, COUNT(*) as vistas
        FROM clics
        WHERE link_uuid = ?
        GROUP BY origin
        ORDER BY vistas DESC
        ';
        $clic = $this->consult($sql, [$link_uuid]);
        $data = $clic->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
}

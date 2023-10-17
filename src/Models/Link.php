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
        $sql = 'SELECT * FROM direcciones WHERE user_uuid = ?';
        $view = $this->consult($sql, [$user_uuid]);
        $data = $view->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }
    
    public funtion editLink($link_name, $link_short, $link_real, $link_uuid, $user_uuid)
    {
      if(empty($link_name) || empty($link_short) || empty($link_real)){
        $response['status'] = 'error';
        $response['message'] = 'Debes completar todos los campos.';
        return $response;
      }else{
        $sql = 'UDPDATE direcciones SET link_name = ?, link_short = ?, link_real = ? WHERE link_uuid = ? AND user_uuid = ?';
        $edit = $this->consult($sql, [$link_name, $link_short, $link_real, $link_uuid, $user_uuid]);
        if($edit){
          $response['status'] = 'OK';
          $response['message'] = 'Debes completar todos los campos.';
          return $response;
        }
      }
    }

}

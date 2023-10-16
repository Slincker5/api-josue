<?php

namespace App\Models;

class Database
{
    private static $parametros = "mysql:host=localhost;dbname=acortador";
    private static $usuario = "root";
    private static $clave = "";

    public function connect()
    {
        try {
            $con = new \PDO(self::$parametros, self::$usuario, self::$clave);
            return $con;
        } catch (\PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }

    protected function consult($sql, $params = [])
    {
        $conexion = $this->connect();
        $consulta = $conexion->prepare($sql);
        $consulta->execute($params);
        return $consulta;
    }

}

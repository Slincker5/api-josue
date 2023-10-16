<?php

namespace App\Models;

use App\Models\Database;
use Firebase\JWT\JWT;
use Ramsey\Uuid\UuidFactory;

class Auth extends Database
{

    #PROPIEDADES CLASE

    private $expReg = '/^[a-zA-Z0-9]+$/';
    private $response = [];

    #METODOS CLASE
    private function usernameStock($user)
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE username = ?';
        $getData = $this->consult($sql, [$user]);
        $total = $getData->fetchColumn();
        return $total;
    }

    public function createAccount($username, $pass)
    {
        if (empty($username) || empty($pass)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Completa todos los campos.';
            return $this->response;
        } else if (!preg_match($this->expReg, $username)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario solo puede contener numeros y letras, no se permiten espacios';
            return $this->response;
        } else if (strlen($username) > 12) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario no puede tener mas de 12 caracteres.';
            return $this->response;
        } else if (strlen($username) < 4) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario debe tener al menos 4 caracteres.';
            return $this->response;
        } else if (strlen($pass) < 8) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Tu contraseña debe tener al menos 8 caracteres.';
            return $this->response;
        } else if ($this->usernameStock($username)) {
            $this->response['status'] = 'error';
            $this->response['message'] = 'El nombre de usuario ya existe, escoge otro diferente';
            return $this->response;
        } else {

            #GENERANDO UN UUID UNICO PARA EL PERFIL
            $uuidFactory = new UuidFactory();
            $uuid = $uuidFactory->uuid4();
            $profile_uuid = $uuid->toString();

            #ENCRIPTADO DE CLAVE
            $options = ['cost' => 12];
            $passwordHash = password_hash($pass, PASSWORD_BCRYPT, $options);

            #PROCEDER AL GUARDADO PERSISTENTE
            $sql = 'INSERT INTO usuarios (user_uuid, username, pass, rol) VALUES (?, ?, ?, ?)';
            $signUp = $this->consult($sql, [$profile_uuid, $username, $passwordHash, 'User']);

            if ($signUp) {
                $key = getenv('JWT_KEY');
                // Crear un token
                $payload = array(
                    "iss" => "mxclick",
                    "aud" => $profile_uuid,
                    "iat" => time(),
                    "nbf" => time(),
                    "data" => array(
                        "user_uuid" => $profile_uuid,
                        "username" => $username,
                        "photo" => '',
                        "rol" => 'User'
                    ),
                );
                $alg = "HS256";
                $token = JWT::encode($payload, $key, $alg);

                $this->response['status'] = 'OK';
                $this->response['message'] = 'Registro exitoso.';
                $this->response['username'] = $username;
                $this->response['user_uuid'] = $profile_uuid;
                $this->response['token'] = $token;

                return $this->response;
            } else {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Hubo algun problema a la hora de tu registro, intenta mas tarde.';
                return $this->response;
            }
        }
    }

    public function logIn($username, $pass)
    {
        $sql = 'SELECT * FROM usuarios WHERE username = ?';
        $logIn = $this->consult($sql, [$username]);
        $accountData = $logIn->fetchAll(\PDO::FETCH_ASSOC);

        if (count($accountData) === 1) {
            if (password_verify($pass, $accountData[0]['pass'])) {

                $key = getenv('JWT_KEY');
                // Crear un token
                $payload = array(
                    "iss" => "mxclick",
                    "aud" => $accountData[0]['user_uuid'],
                    "iat" => time(),
                    "nbf" => time(),
                    "data" => array(
                        "user_uuid" => $accountData[0]['user_uuid'],
                        "username" => $accountData[0]['username'],
                        "photo" => $accountData[0]['photo'],
                        "rol" => $accountData[0]['rol'],
                        "fecha" => $accountData[0]['fecha'],

                    ),
                );
                $alg = "HS256";
                $token = JWT::encode($payload, $key, $alg);

                $this->response['status'] = 'OK';
                $this->response['message'] = 'Sesión exitosa.';
                $this->response['username'] = $username;
                $this->response['user_uuid'] = $accountData[0]['user_uuid'];
                $this->response['photo'] = $accountData[0]['photo'];
                $this->response['rol'] = $accountData[0]['rol'];
                $this->response['token'] = $token;
                return $this->response;
            } else {
                $this->response['status'] = 'error';
                $this->response['message'] = 'Usuario o contraseña incorrectos, valida tus datos';
                return $this->response;
            }
        } else {
            $this->response['status'] = 'error';
            $this->response['message'] = 'Usuario o contraseña incorrectos, valida tus datos';
            return $this->response;
        }
    }

}

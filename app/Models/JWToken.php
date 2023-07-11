<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;


class JWToken{

    private static $key = "adminadmin";

    private static $token = array(
        "iat" => "",
        "mail" => "",
        "tipo" => "",
    );

    public static function CodificarToken($mail,$tipo){        
        $fecha = new Datetime("now", new DateTimeZone('America/Buenos_Aires'));
        JWToken::$token["iat"] = $fecha->getTimestamp();
        JWToken::$token["mail"] = $mail; 
        JWToken::$token["tipo"] = $tipo; 
        $jwt = JWT::encode(JWToken::$token, JWToken::$key,"HS256");

        return $jwt;
    }    

    public static function DecodificarToken($token){
        try
        {            
            $payload = JWT::decode($token,new Key(JWToken::$key, 'HS256'));
            $decoded = array("Estado" => "OK", "Mensaje" => "OK", "Payload" => $payload);
        }
        catch(\Firebase\JWT\BeforeValidException $e){
            $mensaje = $e->getMessage();
            $decoded = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        }
        catch(\Firebase\JWT\ExpiredException $e){
            $mensaje = $e->getMessage();
            $decoded = array("Estado" => "ERROR", "Mensaje" => "$mensaje.");
        }
        catch(Firebase\JWT\SignatureInvalidException $e){
            $mensaje = $e->getMessage();
            $decoded = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        }
        catch(Exception $e){
            $mensaje = $e->getMessage();
            $decoded = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        }        
        return $decoded;
    }
}
?>
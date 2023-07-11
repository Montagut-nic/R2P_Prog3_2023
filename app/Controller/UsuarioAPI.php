<?php

class UsuarioAPI extends Usuario
{
    //similar a un logueo
    public function VerificarUsuario($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $mail = $parametros["mail"];
        $tipo = $parametros["tipo"];
        $clave = $parametros["clave"];
        $retorno = Usuario::Verificar($mail, $tipo, $clave);
        
        if (!array_key_exists('Estado', $retorno)) {
            $token = JWToken::CodificarToken($mail, $retorno["tipo"]);
            $response->getBody()->write(json_encode(array("Estado" => "OK", "Mensaje" => "Verificado exitosamente.", "Token" => $token, "tipo_usuario" => $retorno["tipo"])));
        } else {
            $response->getBody()->write(json_encode($retorno));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function AltaUsuario($request, $response, $args){
        $parametros = $request->getParsedBody();
        $mail = $parametros["mail"];
        $clave = $parametros["clave"];
        $tipo = $parametros["tipo"];

        $resp = json_encode(Usuario::NuevoUsuario($mail, $tipo, $clave));
        $response->getBody()->write($resp);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarCompradores($request, $response, $args)
    {
        $moneda = $args["moneda"];
        $response->getBody()->write(json_encode(Usuario::ListarCompradoresDe($moneda)));
        return $response->withHeader('Content-Type', 'application/json');
    }

}

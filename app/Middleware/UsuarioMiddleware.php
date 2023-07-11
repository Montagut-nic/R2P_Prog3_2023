<?php

use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UsuarioMiddleware
{
    public static function ValidarUsuario(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeader("Authorization");
        if (!empty($header)) {
            $token = explode(' ', $header[0]);
            $validacionToken = JWToken::DecodificarToken($token[1]);
            if ($validacionToken["Estado"] == "OK") {
                $request = $request->withAttribute("payload", $validacionToken["Payload"]);
                $response = $handler->handle($request);
            } else {
                $response = new Response();
                $response->getBody()->write(json_encode($validacionToken));
            }
        }else{
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "No tienes permiso para realizar esta accion (Solo usuarios registrados)");
            $response = new Response();
            $response->getBody()->write(json_encode($respuesta));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarAdmin(Request $request, RequestHandler $handler)
    {
        $payload = $request->getAttribute("payload");

        if ($payload->tipo == "admin") {
            $response = $handler->handle($request);
        } else {
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "No tienes permiso para realizar esta accion (Solo admin)");
            $response = new Response();
            $response->getBody()->write(json_encode($respuesta));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function GuardarAccionLogs(Request $request, RequestHandler $handler)
    {
        $response = $handler->handle($request);
        $body=json_decode($response->getBody(),true);
        if($body['Estado']=='ERROR'){
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $id_moneda = $route->getArgument('id');
        $payload=$request->getAttribute('payload');
        $mail_user = $payload->mail;
        $id_user = Usuario::BuscarIdPorMail($mail_user);

        if(array_key_exists('Estado',$id_user)){
            $respuesta = $id_user;
            $response = new Response();
            $response->getBody()->write(json_encode($respuesta));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $respuesta = Logs::GuardarRegistro($id_user['id'],$id_moneda,$request->getMethod());
        $response->getBody()->write(json_encode($respuesta));
        return $response->withHeader('Content-Type', 'application/json');

    }

}

<?php

class CriptoAPI extends Cripto
{

    public function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        $precio = $parametros["precio"];
        $nombre = $parametros["nombre"];
        $nacionalidad = $parametros["nacionalidad"];
        $foto = $files["foto"];

        $ext = Foto::ObtenerExtension($foto);
        if ($ext != "ERROR") {
            $rutaFoto = "./Fotos/" . $nombre . $ext;
            Foto::GuardarFoto($foto, $rutaFoto);
            $response->getBody()->write(json_encode(Cripto::AltaCripto($precio, $nombre, $nacionalidad, $rutaFoto)));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "Ocurrio un error con la foto");
            $response->getBody()->write(json_encode($respuesta));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function ListarTodos($request, $response, $args)
    {
        $payload = Cripto::Listar();
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarNacionalidad($request, $response, $args)
    {
        $nacionalidad = $args["nacionalidad"];
        $response->getBody()->write(json_encode(Cripto::BuscarNacionalidad($nacionalidad)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args["id"];
        $response->getBody()->write(json_encode(Cripto::BuscarPorId($id)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Borrar($request, $response, $args)
    {
        $id = $args["id"];
        $response->getBody()->write(json_encode(Cripto::Baja($id)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarCripto($request, $response, $args)
    {
        $id = $args["id"];
        $datosPUT = json_decode(file_get_contents("php://input"), true);
        $nombre=$datosPUT['nombre'];
        $nacionalidad=$datosPUT['nacionalidad'];
        $precio=$datosPUT['precio'];

        $response->getBody()->write(json_encode(Cripto::Modificar($id, $nombre, $nacionalidad, $precio)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function GuardarCSV($request, $response, $args)
    {
        $resultado=Cripto::GuardarCriptosCSV();
        $response=$response->withHeader('Content-Type', 'application/csv; charset=UTF-8')
            ->withHeader('Content-Disposition', 'attachment; filename="listado_criptomonedas.csv"');
        if($resultado['Estado']!='OK'){
            $response->getBody()->write(json_encode($resultado));
            $response=$response->withoutHeader('Content-Disposition')
            ->withHeader('Content-Type', 'application/json');
        }
        return $response;
    }
}

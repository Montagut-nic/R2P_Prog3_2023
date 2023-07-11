<?php

class VentaCriptoAPI extends VentaCripto
{

    public function Alta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        $id_cripto = $parametros["id_cripto"];
        $cantidad = $parametros["cantidad"];
        $fecha = date('Y-m-d');
        $foto = $files["foto"];

        $payload = $request->getAttribute("payload");
        $criptomoneda = Cripto::BuscarPorId($id_cripto);
        $ext = Foto::ObtenerExtension($foto);

        if ($ext == "ERROR") {
            $response->getBody()->write(json_encode(array("Estado" => "ERROR", "Mensaje" => "Archivo invalido para foto")));
            return $response->withHeader('Content-Type', 'application/json');
        }
        if (array_key_exists('Estado', $criptomoneda)) {
            $response->getBody()->write(json_encode($criptomoneda));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $pago = intval($criptomoneda['precio']) * intval($cantidad);
        $rutaFoto = "./FotosCripto2023/" . $criptomoneda["nombre"] . '_' . $fecha . '_' . explode('@', $payload->mail)[0] . $ext;
        Foto::GuardarFoto($foto, $rutaFoto);

        $response->getBody()->write(json_encode(VentaCripto::AltaVentaCripto($criptomoneda["nombre"], $id_cripto, $payload->mail, $cantidad, $pago, $rutaFoto)));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarAlemFecha($request, $response, $args)
    {
        $criptosAlemanas = Cripto::BuscarNacionalidad('aleman');
        $resp = [];
        foreach ($criptosAlemanas as $cripto) {
            $ventasCriptoAlem = VentaCripto::BuscarPorIdCripto($cripto['id']);
            foreach ($ventasCriptoAlem as $venta) {
                date_default_timezone_set("America/Argentina/Buenos_Aires");
                $fechaVenta = DateTime::createFromFormat('Y-m-d', $venta['fecha']);
                $fechaMin = DateTime::createFromFormat('Y-m-d', '2023-07-10');
                $fechaMax = DateTime::createFromFormat('Y-m-d', '2023-07-13');
                if ($fechaVenta >= $fechaMin && $fechaVenta <= $fechaMax) {
                    $resp[] =  $venta;
                }
            }
        }
        $response->getBody()->write(json_encode($resp));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarVentasNacionalidadYFecha($request, $response, $args)
    {
        $fechaMin = $args['MinAnno'] . '-' . $args['MinMes'] . '-' . $args['MinDia'];
        $fechaMax = $args['MaxAnno'] . '-' . $args['MaxMes'] . '-' . $args['MaxDia'];
        $resp = VentaCripto::BuscarPorNacionalidadYFecha($args['nacionalidad'], $fechaMin, $fechaMax);
        $response->getBody()->write(json_encode($resp));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function GuardarPDF($request, $response, $args)
    {
        $ordenamiento=$args['orden'];
        $resultado = VentaCripto::GuardarVentasPDF($ordenamiento);
        $response = $response->withHeader('Content-Type', 'application/pdf; charset=UTF-8')
            ->withHeader('Content-Disposition', 'attachment; filename="VentasCriptomonedas.pdf"');
        if ($resultado['Estado'] != 'OK') {
            $response->getBody()->write(json_encode($resultado));
            $response = $response->withoutHeader('Content-Disposition')
                ->withHeader('Content-Type', 'application/json');
        }
        return $response;
    }
}

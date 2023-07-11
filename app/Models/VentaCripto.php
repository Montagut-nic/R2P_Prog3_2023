<?php

use Fpdf\Fpdf;

class VentaCripto
{
    public $pago;
    public $mail_comprador;
    public $nombre_cripto;
    public $fecha;
    public $foto;
    public $cantidad;
    public $id_cripto;
    public $id_venta;

    public static function AltaVentaCripto($nombre_cripto, $id_cripto, $mail_comprador, $cantidad, $pago, $path_foto)
    {

        $fecha = $fecha = date('Y-m-d H:i');
        $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();
        $respuesta = "";
        try {
            $consulta = $objetoAccesoDato->PrepararConsulta("INSERT INTO ventas (nombre_cripto, id_cripto, mail_comprador, fecha, cantidad, pago, foto) 
                                                            VALUES (:nombre_cripto, :id_cripto, :mail_comprador, :fecha, :cantidad, :pago, :foto);");

            $consulta->bindValue(':nombre_cripto', $nombre_cripto, PDO::PARAM_STR);
            $consulta->bindValue(':id_cripto', $id_cripto, PDO::PARAM_INT);
            $consulta->bindValue(':mail_comprador', $mail_comprador, PDO::PARAM_STR);
            $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
            $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':pago', $pago, PDO::PARAM_INT);
            $consulta->bindValue(':foto', $path_foto, PDO::PARAM_STR);

            $consulta->execute();

            $respuesta = array("Estado" => "OK", "Mensaje" => "Venta registrada correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    public static function BuscarPorIdCripto($id_cripto)
    {
        try {
            $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();

            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT id as id_venta, nombre_cripto, id_cripto, mail_comprador, fecha, cantidad, pago, foto FROM criptos WHERE id_cripto = :id");
            $consulta->bindValue(':id', $id_cripto, PDO::PARAM_INT);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "VentaCripto");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function BuscarPorNacionalidadYFecha($nacionalidad, $fechaMin, $fechaMax)
    {
        try {
            $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();

            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT ventas.id as id_venta, nombre_cripto, id_cripto, mail_comprador, fecha, cantidad, pago, ventas.foto FROM ventas 
                                                        INNER JOIN criptos ON ventas.id_cripto = criptos.id WHERE criptos.nacionalidad = :nacionalidad AND fecha BETWEEN :fecha_min AND :fecha_max");
            $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_min', $fechaMin, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_max', $fechaMax, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "VentaCripto");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function TraerUltimoMes()
    {
        try {
            $fechaMin = date('Y-m-d', strtotime('-1 month'));
            $fechaMax = date('Y-m-d');

            $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();
            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT ventas.id as id_venta, nombre_cripto, id_cripto, mail_comprador, fecha, cantidad, pago, ventas.foto FROM ventas 
                                                        INNER JOIN criptos ON ventas.id_cripto = criptos.id WHERE fecha BETWEEN :fecha_min AND :fecha_max");
            $consulta->bindValue(':fecha_min', $fechaMin, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_max', $fechaMax, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
            if (empty($resultado)) {
                $resultado = array("Estado" => "ERROR", "Mensaje" => "No hay ventas registradas en el ultimo mes");
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    private static function sortFechaAsc($a, $b)
    {
        $fechaA = DateTime::createFromFormat('Y-m-d', $a['fecha']);
        $fechaB = DateTime::createFromFormat('Y-m-d', $b['fecha']);
        return $fechaA <=> $fechaB;
    }

    private static function sortFechaDes($a, $b)
    {
        $fechaA = DateTime::createFromFormat('Y-m-d', $a['fecha']);
        $fechaB = DateTime::createFromFormat('Y-m-d', $b['fecha']);
        return $fechaB <=> $fechaA;
    }

    public static function GuardarVentasPDF($orden)
    {
        try {
            $ventas = VentaCripto::TraerUltimoMes();
            if (array_key_exists('Estado', $ventas)) {
                return $ventas;
            }
            if ($orden == 'asc') {
                usort($ventas, 'self::sortFechaAsc');
            }else{
                usort($ventas, 'self::sortFechaDes');
            }

            $pdf = new FPDF("L", "mm", "A4");
            $pdf->AddPage();
            $pdf->SetFont("Arial", "B", 10);
            //ancho,largo,contenido,borde(T/F),salto de linea(T/F),alineacion(C=center/R=right)
            $pdf->Cell(20, 10, 'ID venta', 1, 0, "C");
            $pdf->Cell(20, 10, 'ID cripto', 1, 0, "C");
            $pdf->Cell(30, 10, 'criptomoneda', 1, 0, "C");
            $pdf->Cell(20, 10, 'cantidad', 1, 0, "C");
            $pdf->Cell(20, 10, 'pago', 1, 0, "C");
            $pdf->Cell(30, 10, 'comprador', 1, 0, "C");
            $pdf->Cell(30, 10, 'fecha', 1, 0, "C");
            $pdf->Cell(90, 10, 'foto', 1, 1, "C");

            foreach ($ventas as $item) {
                $pdf->Cell(20, 10, $item['id_venta'], 1, 0, "C");
                $pdf->Cell(20, 10, $item['id_cripto'], 1, 0, "C");
                $pdf->Cell(30, 10, $item['nombre_cripto'], 1, 0, "C");
                $pdf->Cell(20, 10, $item['cantidad'], 1, 0, "C");
                $pdf->Cell(20, 10, $item['pago'], 1, 0, "C");
                $pdf->Cell(30, 10, $item['mail_comprador'], 1, 0, "C");
                $pdf->Cell(30, 10, $item['fecha'], 1, 0, "C");
                $pdf->Cell(90, 10, $item['foto'], 1, 1, "C");
            }

            $pdf->Output("D", "VentasCriptomonedas.pdf", true);
            $resultado = array("Estado" => "OK");
            return $resultado;
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
            return $resultado;
        }
    }

    public static function GuardarVentasCSV($orden)
    {
        try {
            $ventas = VentaCripto::TraerUltimoMes();
            if(array_key_exists('Estado',$ventas)){
                return $ventas;
            }
            if ($orden == 'asc') {
                usort($ventas, 'self::sortFechaAsc');
            }else{
                usort($ventas, 'self::sortFechaDes');
            }
            $archivo = fopen('php://output', 'w');
            if ($archivo != null) {
                foreach ($ventas as $item) {
                    fputcsv($archivo, $item);
                }
                fclose($archivo);
                $resultado = array("Estado" => "OK");
                return $resultado;
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
            return $resultado;
        }
    }

}

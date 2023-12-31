<?php
use Fpdf\Fpdf;

class Logs
{

    public $id_usuario;
    public $id_cripto;
    public $accion;
    public $fecha_accion;


    public static function GuardarRegistro($id_usuario, $id_cripto, $metodo)
    {
        $accion = '';
        switch ($metodo) {
            case 'DELETE':
                $accion = 'Borrado';
                break;
            default:
                $accion = 'Desconocido';
                break;
        }
        $fecha=date('Y-m-d');
        $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();
        try {
            $consulta = $objetoAccesoDato->PrepararConsulta("INSERT INTO logs (id_usuario, id_cripto, accion, fecha_accion) 
                                                            VALUES (:id_us, :id_cr, :acc, :fecha);");

            $consulta->bindValue(':id_us', $id_usuario, PDO::PARAM_INT);
            $consulta->bindValue(':id_cr', $id_cripto, PDO::PARAM_INT);
            $consulta->bindValue(':acc', $accion, PDO::PARAM_STR);
            $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);

            $consulta->execute();

            $resultado = array("Estado" => "OK", "Mensaje" => "logs registrados correctamente.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }

    }

    public static function Listar()
    {
        $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();
        try {
            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT id_usuario, id_cripto, accion, fecha_accion FROM logs;");
            $consulta->execute();
            $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultado)) {
                $resultado = array("Estado" => "ERROR", "Mensaje" => "No hay logs registrados");
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function GuardarLogsCSV()
    {
        try {
            $ar_logs=Logs::Listar();
            if(array_key_exists('Estado',$ar_logs)){
                return $ar_logs;
            }
            $archivo = fopen('php://output', 'w');
            if ($archivo != null) {
                foreach ($ar_logs as $item) {
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

    public static function GuardarLogsPDF()
    {
        try {
            $ar_logs=Logs::Listar();
            if (array_key_exists('Estado', $ar_logs)) {
                return $ar_logs;
            }

            $pdf = new FPDF("P", "mm", "A4");
            $pdf->AddPage();
            $pdf->SetFont("Arial", "B", 12);
            //ancho,largo,contenido,borde(T/F),salto de linea(T/F),alineacion(C=center/R=right)
            $pdf->Cell(25, 10, 'ID usuario', 1, 0, "C");
            $pdf->Cell(20, 10, 'ID cripto', 1, 0, "C");
            $pdf->Cell(30, 10, 'accion', 1, 0, "C");
            $pdf->Cell(30, 10, 'fecha', 1, 1, "C");

            foreach ($ar_logs as $item) {
                $pdf->Cell(25, 10, $item['id_usuario'], 1, 0, "C");
                $pdf->Cell(20, 10, $item['id_cripto'], 1, 0, "C");
                $pdf->Cell(30, 10, $item['accion'], 1, 0, "C");
                $pdf->Cell(30, 10, $item['fecha_accion'], 1, 1, "C");
            }

            $pdf->Output("D", "RegistroLogs.pdf", true);
            $resultado = array("Estado" => "OK");
            return $resultado;
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
            return $resultado;
        }
    }
}

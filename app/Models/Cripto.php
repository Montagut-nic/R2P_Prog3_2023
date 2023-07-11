<?php

class Cripto
{
    public $precio;
    public $nombre;
    public $foto;
    public $nacionalidad;
    public $id;

    public static function AltaCripto($precio, $nombre, $nacionalidad, $path_foto)
    {
        $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();
        try {
            $consulta = $objetoAccesoDato->PrepararConsulta("INSERT INTO criptos (precio, nombre, nacionalidad, foto) 
                                                            VALUES (:precio, :nombre, :nacionalidad, :foto);");

            $consulta->bindValue(':precio', $precio, PDO::PARAM_INT);
            $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
            $consulta->bindValue(':foto', $path_foto, PDO::PARAM_STR);

            $consulta->execute();

            $resultado = array("Estado" => "OK", "Mensaje" => "Criptomoneda registrada correctamente.");
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
            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT id, precio, nombre, nacionalidad, foto FROM criptos");
            $consulta->execute();
            $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultado)) {
                $resultado = array("Estado" => "ERROR", "Mensaje" => "No hay criptomonedas registradas");
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function BuscarNacionalidad($nacionalidad)
    {
        try {
            $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();

            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT id, precio, nombre, nacionalidad, foto FROM criptos WHERE nacionalidad = :nacion");
            $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
            $consulta->execute();
            $resultado = $consulta->fetchAll(PDO::FETCH_CLASS, "Cripto");

            if (empty($resultado)) {
                $resultado = array("Estado" => "ERROR", "Mensaje" => "No hay criptomonedas registradas con esa nacionalidad");
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function BuscarPorId($id)
    {
        try {
            $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();

            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT id, precio, nombre, nacionalidad, foto FROM criptos WHERE id = :id;");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($resultado == false) {
                $resultado = array("Estado" => "ERROR", "Mensaje" => "No hay criptomonedas registradas con ese ID");
            }
            return $resultado;
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
            return $resultado;
        }
    }

    public static function Baja($id)
    {
        $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();
        try {
            $consulta = $objetoAccesoDato->PrepararConsulta("DELETE FROM criptos  
                                                            WHERE id = :id;");

            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();

            $resultado = array("Estado" => "OK", "Mensaje" => "Criptomoneda eliminada de los registros.");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function Modificar($id, $nombre, $nacionalidad, $precio)
    {
        $datos_viejos = Cripto::BuscarPorId($id);
        $vieja_foto = $datos_viejos['foto'];
        $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();
        try {
            $rutaFoto = './Fotos/' . $nombre . '.jpg';

            $consulta = $objetoAccesoDato->PrepararConsulta("UPDATE criptos
                                                    SET nombre = :new_nombre, precio = :new_precio, nacionalidad = :new_nacionalidad, foto = :new_ruta  
                                                    WHERE id = :id;");

            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->bindValue(':new_nombre', $nombre, PDO::PARAM_STR);
            $consulta->bindValue(':new_precio', $precio, PDO::PARAM_INT);
            $consulta->bindValue(':new_nacionalidad', $nacionalidad, PDO::PARAM_STR);
            $consulta->bindValue(':new_ruta', $rutaFoto, PDO::PARAM_STR);
            $consulta->execute();

            if (file_exists($vieja_foto)) {
                rename($vieja_foto, $rutaFoto);
            }

            $resultado = array("Estado" => "OK", "Mensaje" => "Criptomoneda $id modificada");
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function GuardarCriptosCSV(){
        try {
            $ar_criptos=Cripto::Listar();
            if(array_key_exists('Estado',$ar_criptos)){
                return $ar_criptos;
            }
            $archivo = fopen('php://output', 'w');
            if ($archivo != null) {
                foreach ($ar_criptos as $item) {
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

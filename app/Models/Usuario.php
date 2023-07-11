<?php

class Usuario
{

    public $mail;
    public $tipo;
    public $clave;
    public $id;

    public static function Verificar($mail, $tipo, $password)
    {
        try {
            $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();

            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT mail, tipo, clave, id FROM usuarios 
                                                            WHERE mail = :mail AND clave = :password AND tipo = :tipo");

            $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
            $consulta->bindValue(':password', $password, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
            $consulta->execute();
            $resultado = $consulta->fetch();

            if (empty($resultado)) {
                $resultado = array("Estado" => "ERROR", "Mensaje" => "Usuario, tipo o clave no coinciden.");
            }
            return $resultado;
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
            return $resultado;
        }
    }

    public static function NuevoUsuario($mail, $tipo, $password)
    {
        $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();
        $respuesta = "";
        try {

            if ($tipo == 'cliente' || $tipo == 'admin') {
                $consulta = $objetoAccesoDato->PrepararConsulta("INSERT INTO usuarios (mail, tipo, clave) 
                VALUES (:mail, :tipo, :clave);");

                $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
                $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
                $consulta->bindValue(':clave', $password, PDO::PARAM_STR);
                $consulta->execute();

                $respuesta = array("Estado" => "OK", "Mensaje" => "Empleado registrado correctamente.");
            } else {
                $respuesta = array("Estado" => "ERROR", "Mensaje" => "Ingrese un tipo de usuario valido: admin o cliente");
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $respuesta = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $respuesta;
        }
    }

    public static function ListarCompradoresDe($moneda)
    {
        try {
            $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();

            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT usuarios.mail, usuarios.tipo, usuarios.id FROM usuarios 
                                                INNER JOIN ventas ON usuarios.mail = ventas.mail_comprador 
                                                WHERE ventas.nombre_cripto = :nombre GROUP BY usuarios.mail;");

            $consulta->bindValue(':nombre', $moneda, PDO::PARAM_STR);
            $consulta->execute();

            $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
        } finally {
            return $resultado;
        }
    }

    public static function BuscarIdPorMail($mail){
        try {
            $objetoAccesoDato = AccesoDatos::ObtenerObjetoAcceso();

            $consulta = $objetoAccesoDato->PrepararConsulta("SELECT id FROM usuarios WHERE mail = :mail;");

            $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
            $consulta->execute();
            $resultado = $consulta->fetch();

            if (empty($resultado)) {
                $resultado = array("Estado" => "ERROR", "Mensaje" => "No se encontro usuario con ese mail");
            }
            return $resultado;
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $resultado = array("Estado" => "ERROR", "Mensaje" => "$mensaje");
            return $resultado;
        }
    }
}

<?php

class LogsAPI extends Logs
{
    public static function GuardarCSV($request, $response, $args)
    {
        $resultado=Logs::GuardarLogsCSV();
        $response=$response->withHeader('Content-Type', 'application/csv; charset=UTF-8')
            ->withHeader('Content-Disposition', 'attachment; filename="registro_logs.csv"');
        if($resultado['Estado']!='OK'){
            $response->getBody()->write(json_encode($resultado));
            $response=$response->withoutHeader('Content-Disposition')
            ->withHeader('Content-Type', 'application/json');
        }
        return $response;
    }
}
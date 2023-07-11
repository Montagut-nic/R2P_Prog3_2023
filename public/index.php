<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Selective\BasePath\BasePathMiddleware;

require __DIR__ . '../../vendor/autoload.php';

//DB
include_once ('../app/DB/AccesoDatos.php');

//Models
include_once ('../app/Models/Usuario.php');
include_once ('../app/Models/Cripto.php');
include_once ('../app/Models/VentaCripto.php');
include_once ('../app/Models/JWToken.php');
include_once ('../app/Models/Foto.php');
include_once ('../app/Models/Logs.php');

//Controllers
include_once ('../app/Controller/UsuarioAPI.php');
include_once ('../app/Controller/CriptoAPI.php');
include_once ('../app/Controller/VentaCriptoAPI.php');
include_once ('../app/Controller/LogsAPI.php');

//MiddleWares
include_once ('../app/Middleware/UsuarioMiddleware.php');


// Instantiate App
$app = AppFactory::create();
$app->setBasePath('/2doParcialP3/public');

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addRoutingMiddleware();

// Add parse body
$app->addBodyParsingMiddleware();

date_default_timezone_set("America/Argentina/Buenos_Aires");

$app->post('/usuario/verificar[/]', \UsuarioAPI::class . ':VerificarUsuario'); 
$app->post('/usuario/alta[/]', \UsuarioAPI::class . ':AltaUsuario')
->add(\UsuarioMiddleware::class . ':ValidarAdmin')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->post('/criptomoneda/alta[/]', \CriptoAPI::class . ':Alta')
->add(\UsuarioMiddleware::class . ':ValidarAdmin')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->get('/criptomoneda/listar[/]', \CriptoAPI::class . ':ListarTodos');
$app->get('/criptomoneda/listar/{nacionalidad}[/]', \CriptoAPI::class . ':ListarNacionalidad');
$app->get('/criptomoneda/mostrar/{id}[/]', \CriptoAPI::class . ':TraerUno')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->post('/venta/alta[/]', \VentaCriptoAPI::class . ':Alta')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->get('/ventas/{nacionalidad}/{MinDia}/{MinMes}/{MinAnno}/{MaxDia}/{MaxMes}/{MaxAnno}[/]', \VentaCriptoAPI::class . ':ListarVentasNacionalidadYFecha')
->add(\UsuarioMiddleware::class . ':ValidarAdmin')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->get('/usuarios/compradores/{moneda}[/]', \UsuarioAPI::class . ':ListarCompradores')
->add(\UsuarioMiddleware::class . ':ValidarAdmin')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->delete('/criptomoneda/{id}[/]',\CriptoAPI::class . ':Borrar')
->add(\UsuarioMiddleware::class . ':GuardarAccionLogs')
->add(\UsuarioMiddleware::class . ':ValidarAdmin')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->put('/criptomoneda/{id}[/]',\CriptoAPI::class . ':ModificarCripto')
->add(\UsuarioMiddleware::class . ':ValidarAdmin')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->get('/criptomoneda/descargar[/]', \CriptoAPI::class . ':GuardarCSV');
$app->get('/criptomoneda/descargar/{id}[/]', \CriptoAPI::class . ':GuardarPDF');
$app->get('/logs/descargar/CSV[/]', \LogsAPI::class . ':GuardarCSV')
->add(\UsuarioMiddleware::class . ':ValidarAdmin')
->add(\UsuarioMiddleware::class . ':ValidarUsuario');
$app->get('/logs/descargar/PDF[/]', \LogsAPI::class . ':GuardarPDF');
$app->get('/ventas/descargar/PDF/{orden}[/]', \VentaCriptoAPI::class . ':GuardarPDF');
$app->get('/ventas/descargar/CSV/{orden}[/]', \VentaCriptoAPI::class . ':GuardarCSV');



$app->run();
<?php

//header('Access-Control-Allow-Origin: *');
//header('Content-type: application/json');

//date_default_timezone_set("America/São_Paulo");
//$path =$_GET['path'];


include 'controllers/EmpresaController.php';
include 'controllers/CampanhaController.php';
include 'controllers/ParticipanteController.php';


$requestMethod = $_SERVER['REQUEST_METHOD'];
//echo  $requestMethod;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', $path);

$controller = new EmpresaController();
$controllerCampanha = new CampanhaController();
$controllerParticipante = new ParticipanteController();


/*
echo("<br>");
echo(" dir UM ");
print_r($pathParts[1]);
echo("<br>");
echo(" dir DOIS");

print_r($pathParts[2]);
echo("<br>")
*/


if ($pathParts[2] === 'empresa') {
    switch ($requestMethod) {
        case 'GET':

            if (isset($pathParts[3]) && is_numeric($pathParts[3])) {
                $controller->show($pathParts[3]);
            } else {
                $controller->index();
            }

            break;
        case 'POST':
 
            $controller->create();
            break;

        case 'PUT':
        case 'PATCH':
            echo  ("caiu no UPDATE");
            $requestData = json_decode(file_get_contents('php://input'), true);
            $controller->update($requestData['cnpj'], $requestData);

            break;
        case 'DELETE':
 
            $requestData = json_decode(file_get_contents('php://input'), true);
              $cnpj = $requestData['cnpj'];
              $controller = new EmpresaController();
              $controller->delete($cnpj);
            
            break;
        default:
            // Método não suportado
            header('HTTP/1.1 405 Method Not Allowed');
            break;
    }
}else if ($pathParts[2] === 'campanha') {
    switch ($requestMethod) {
        case 'GET':

            if (isset($pathParts[3]) && is_numeric($pathParts[3])) {
                $controllerCampanha->show($pathParts[3]);
            } else {
                $controllerCampanha->index();
            }
            
            break;

        case 'POST':
            
            $controllerCampanha->create();
            break;

        case 'PUT':
        case 'PATCH':
            echo  ("caiu no UPDATE");
            $requestData = json_decode(file_get_contents('php://input'), true);
            $controllerCampanha->update($requestData['id'], $requestData);
            break;

        case 'DELETE':
              $requestData = json_decode(file_get_contents('php://input'), true);
              $cnpj = $requestData['id'];
              $controllerCampanha = new CampanhaController();
              $controllerCampanha->delete($cnpj);
            break;

        default:
            // Método não suportado
            header('HTTP/1.1 405 Method Not Allowed');
            break;
    }
} else if ($pathParts[2] === 'participante') {
    switch ($requestMethod) {
        case 'GET':
            if (isset($pathParts[3]) && is_numeric($pathParts[3])) {
                $controllerParticipante->show($pathParts[3]);
            } else {
                $controllerParticipante->index();
            }
            break;

        case 'POST':
      
            $controllerParticipante->create();
            break;

        case 'PUT':
        case 'PATCH':
            echo ("caiu no UPDATE");
            $requestData = json_decode(file_get_contents('php://input'), true);
            $controllerParticipante->update($requestData['id'], $requestData);
            break;

        case 'DELETE':
            $requestData = json_decode(file_get_contents('php://input'), true);
            $id = $requestData['id'];
            $controllerParticipante->delete($id);
            break;

        default:
            // Método não suportado
            header('HTTP/1.1 405 Method Not Allowed');
            break;
    }
} else {
    // Rota não encontrada
    header('HTTP/1.1 404 Not Found');
}


/*else {
    // Rota não encontrada
    header('HTTP/1.1 404 Not Found');
}
*/
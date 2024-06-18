<?php
require_once 'config.php';

$action = $_REQUEST['action'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    /*
    //guardar en una cookie el filtro
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
        setcookie('filter', $filter, time() + 3600, '/');
    }
    */
    switch ($action) {
        case 'filteroptions':
            $servicio_sql = "SELECT
                JSON_ARRAYAGG( 
                    DISTINCT cliente
                ) AS clientes,
                JSON_ARRAYAGG( 
                    DISTINCT direccion
                ) AS direcciones,
                JSON_ARRAYAGG( 
                    DISTINCT capacidad
                ) AS capacidades,
                JSON_ARRAYAGG( 
                    DISTINCT esta_vigente
                ) AS esta_vigentes,
                JSON_ARRAYAGG( 
                    DISTINCT moneda
                ) AS monedas
                FROM
                    servicio;
            ";

            $operador_sql = "SELECT
                JSON_ARRAYAGG( 
                    DISTINCT nombre
                ) AS operadores
                FROM
                    operador;
            ";

            $result = $conn->query($servicio_sql);
            $servicio_result = $result->fetch_assoc();

            $result = $conn->query($operador_sql);
            $operador_result = $result->fetch_assoc();

            $response = [];
            foreach ($servicio_result as $key => $value) {
                $response[$key] = json_decode($value);
            }

            foreach ($operador_result as $key => $value) {
                $response[$key] = json_decode($value);
            }

            echo json_encode($response);

            # code...
            break;
        case "data":

            $result = $conn->query("call GetFacturas();");
            $result = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($result as $key => $value) {
                $result[$key]['facturas'] = json_decode($value['facturas']);
            }
            
            echo json_encode($result);

            break;
        default:
            # code...
            break;
    }
}

<?php


// if it was a get we die

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    die();
}

require_once '../config.php';

// Suponiendo que la conexión ya está establecida
$file = fopen("all.csv","r");

// Saltar la primera línea
$header = fgetcsv($file);


// Leer cada línea del archivo CSV
while(($row = fgetcsv($file)) !== FALSE) {
 
    //OPERADOR,ID OPERADOR,ID SERVICIO ORBYTA,CLIENTE/USO,DIRECCION,"CAPACIDAD(mbps)",ORDEN DE COMPRA,ESTA VIGENTE,MONEDA,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,FACTURA,NETO,FACTURA,NETO,FACTURA,NETO,FACTURA,FACTURA,NETO
    // id_operador, nombre
    $nombre = $row[0];
    $id_operador = $row[1];

    // Insertar los datos en la tabla
    $sql = "INSERT INTO operador (id_operador, nombre)
    SELECT '$id_operador', '$nombre'
    WHERE NOT EXISTS (
        SELECT 1
        FROM operador
        WHERE id_operador = '$id_operador' AND nombre = '$nombre')
    RETURNING id;";

    if (!($op_id = mysqli_fetch_assoc(mysqli_query($conn, $sql)))) {
        $op_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM operador WHERE id_operador = '$id_operador' AND nombre = '$nombre'"));
    } 

    $op_id = $op_id['id'];
    $id_servicio_orbyta = $row[2];
    $cliente_uso = $row[3];
    $direccion = $row[4];
    $capacidad = $row[5];
    $orden_de_compra = $row[6];
    $esta_vigente = $row[7] == 'SI' ? 1 : 0;
    $moneda = $row[8];

    /*
    print_r($id_servicio_orbyta);
    print_r($cliente_uso);
    print_r($direccion);
    print_r($capacidad);
    print_r($orden_de_compra);
    die();
    var_dump($esta_vigente);
    var_dump($moneda);
    
    */

    $sql = "INSERT INTO servicio (id_operador, id_servicio_orbyta, cliente, direccion, capacidad, orden_de_compra, esta_vigente, moneda)
        VALUES ($op_id, 
        '$id_servicio_orbyta', 
        '$cliente_uso', 
        '$direccion', 
        '$capacidad', 
        '$orden_de_compra', 
        $esta_vigente,
        '$moneda');"
    ;

    mysqli_query($conn, $sql);

    $sql = "SELECT LAST_INSERT_ID() AS id;";

    $serv_id = mysqli_fetch_assoc( mysqli_query($conn, $sql));
    $serv_id = $serv_id['id'];

    $facturas = array();
    $netos = array();

    // fecha de inicio es 15-10-2021
    $fecha = date('Y-m-d', strtotime('2021-10-15'));
    for ($i = 9; $i < count($row); $i += 2) {

        $factura = $row[$i]== "" ? 0 : $row[$i];

        $sql = "INSERT INTO cargo_mensual (id_servicio, fecha, factura)
            VALUES ($serv_id, '$fecha', $factura);";
        mysqli_query($conn, $sql);
        mysqli_error($conn);
        $fecha = date('Y-m-d', strtotime($fecha . ' + 1 month'));
    }
}



fclose($file);

//redirect to index
header('Location: ../db');
?>
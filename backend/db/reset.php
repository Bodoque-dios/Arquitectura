<?php

// if it was a get we die
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    die();
}

require_once '../config.php';

$sql = <<<EOD
    DROP DATABASE IF EXISTS `arq`;
    CREATE DATABASE `arq`;
    USE `arq`;

    CREATE TABLE operador
    (
        id          INT PRIMARY KEY AUTO_INCREMENT,
        id_operador varchar(255) NOT NULL,
        nombre      VARCHAR(255) NOT NULL
    );

    CREATE TABLE servicio
    (
        id                 INT PRIMARY KEY AUTO_INCREMENT,
        id_operador        INT,
        id_servicio_orbyta varchar(255),
        cliente            VARCHAR(255),
        direccion          VARCHAR(255),
        capacidad          VARCHAR(50),
        orden_de_compra    VARCHAR(255),
        esta_vigente       BOOLEAN,
        moneda             VARCHAR(3),
        FOREIGN KEY (id_operador) REFERENCES operador (id)
    );

    CREATE TABLE cargo_mensual (
        id SERIAL PRIMARY KEY,
        id_servicio INT NOT NULL,
        fecha DATE NOT NULL,
        factura INT NOT NULL,
        FOREIGN KEY (id_servicio) REFERENCES servicio (id)
    );

    CREATE TABLE UF (
        id SERIAL PRIMARY KEY,
        fecha DATE NOT NULL UNIQUE,
        rate FLOAT NOT NULL
    );

    DELIMITER //

    drop procedure if exists GetFacturas;
    CREATE PROCEDURE GetFacturas()
    BEGIN
        DECLARE v_factura INT;
        DECLARE v_tasa DECIMAL(10, 4);
        DECLARE done INT DEFAULT FALSE;

        -- Declare variables for cursor fetch
        DECLARE cur_nombre VARCHAR(255);
        DECLARE cur_id_operador varchar(255);
        DECLARE cur_id_servicio_orbyta VARCHAR(255);
        DECLARE cur_cliente VARCHAR(255);
        DECLARE cur_direccion VARCHAR(255);
        DECLARE cur_capacidad VARCHAR(255);
        DECLARE cur_orden_de_compra VARCHAR(255);
        DECLARE cur_esta_vigente BOOLEAN;
        DECLARE cur_moneda VARCHAR(10);
        DECLARE cur_fecha DATE;
        DECLARE cur_factura INT;
        DECLARE cur_neto INT;

        DECLARE cur CURSOR FOR
            SELECT operador.nombre,
                operador.id_operador,
                servicio.id_servicio_orbyta,
                servicio.cliente,
                servicio.direccion,
                servicio.capacidad,
                servicio.orden_de_compra,
                servicio.esta_vigente,
                servicio.moneda,
                cargo_mensual.fecha,
                cargo_mensual.factura
            FROM operador
                JOIN servicio ON operador.id = servicio.id_operador
                left JOIN cargo_mensual ON servicio.id = cargo_mensual.id_servicio;

        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

        DROP TEMPORARY TABLE IF EXISTS TempResult;
        CREATE TEMPORARY TABLE TempResult (
            nombre VARCHAR(255),
            id_operador varchar(255),
            id_servicio_orbyta varchar(255),
            cliente VARCHAR(255),
            direccion VARCHAR(255),
            capacidad VARCHAR(255),
            orden_de_compra VARCHAR(255),
            esta_vigente BOOLEAN,
            moneda VARCHAR(10),
            facturas JSON,
            PRIMARY KEY (id_operador, id_servicio_orbyta)
        );

        OPEN cur;

        read_loop: LOOP

            FETCH cur INTO cur_nombre, cur_id_operador, cur_id_servicio_orbyta, cur_cliente, cur_direccion, cur_capacidad, cur_orden_de_compra, cur_esta_vigente, cur_moneda, cur_fecha, cur_factura;

            IF done THEN
                LEAVE read_loop;
            END IF;

            -- Get the conversion rate for the given date without triggering NOT FOUND
            SELECT COALESCE(uf.rate, 1) into v_tasa
            FROM (SELECT cur_fecha AS fecha) AS d
            LEFT JOIN UF uf ON d.fecha = uf.fecha;

            IF v_tasa IS NOT NULL THEN
                SET cur_neto = cur_factura * v_tasa;
            ELSE
                SET cur_neto = cur_factura; -- if no conversion rate is found, use the original factura
            END IF;

            INSERT INTO TempResult (
                nombre, id_operador, id_servicio_orbyta, cliente, direccion, capacidad, orden_de_compra, esta_vigente, moneda, facturas
            ) VALUES (
                cur_nombre, cur_id_operador, cur_id_servicio_orbyta, cur_cliente, cur_direccion, cur_capacidad, cur_orden_de_compra, cur_esta_vigente, cur_moneda, JSON_ARRAY(JSON_OBJECT("fecha", cur_fecha, "factura", cur_factura, "neto", cur_neto))
            ) ON DUPLICATE KEY UPDATE
            facturas = JSON_ARRAY_APPEND(facturas, '$', JSON_OBJECT("fecha", cur_fecha, "factura", cur_factura, "neto", cur_neto));
        END LOOP;

        CLOSE cur;

        SELECT
            nombre,
            id_operador,
            id_servicio_orbyta,
            cliente,
            direccion,
            capacidad,
            orden_de_compra,
            esta_vigente,
            moneda,
            JSON_UNQUOTE(JSON_EXTRACT(facturas, '$')) AS facturas
        FROM
            TempResult;

    END //

    DELIMITER ;
    EOD;

if (mysqli_multi_query($conn, $sql)) {
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
}


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


echo "<div style='display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;'>
        <p style='font-size: 20px; color: green;'>Database reset successfully</p>
      </div>";

?>
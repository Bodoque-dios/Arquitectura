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

            case"loadf";

            $result = $conn->query(<<<EOD
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
            
            
            EOD);

echo mysqli_info($conn);

            break;
        default:
            # code...
            break;
    }
}

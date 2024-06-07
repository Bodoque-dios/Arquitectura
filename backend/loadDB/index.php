<?php
$servername = "localhost";
$username = "user";
$password = "";
$dbname = "arq";
$error = 0;

// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Verificar conexión
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully <br>";

// Tu código aquí

// Fetch contacts (in descending order)
$result = mysqli_query($conn, "SELECT * FROM operador ORDER BY id DESC");
// Suponiendo que la conexión ya está establecida
$file = fopen("operador.csv","r");

// Saltar la primera línea
$header = fgetcsv($file);

// Leer cada línea del archivo CSV
while(($row = fgetcsv($file)) !== FALSE) {
    // Asumiendo que el CSV tiene las columnas en el siguiente orden:
    // id_operador, nombre
    $id_operador = $row[1];
    $nombre = $row[0];

    // Insertar los datos en la tabla
    $sql = "INSERT INTO operador (id_operador, nombre) VALUES ('$id_operador', '$nombre')";

    if (mysqli_query($conn, $sql)) {
        
    } else {
        $error = 1;
    }
}

fclose($file); 

// Abrir el archivo CSV
$file = fopen("cliente-uso.csv", "r");

// Saltar la primera línea
$header = fgetcsv($file);

// Leer cada línea del archivo CSV e insertarla en la tabla
while (($row = fgetcsv($file)) !== FALSE) {
    // Asumiendo que el CSV tiene las columnas en el siguiente orden:
    // operador, id-operador, id-servicio-orbyta, cliente/uso, direccion, capacidad, order-de-compra, esta-vigente, moneda
    $operador =  $row[0];
    $id_operador =  $row[1];
    $id_servicio_orbyta =  $row[2];
    $cliente_uso =  $row[3];
    $direccion =  $row[4];
    $capacidad =  $row[5];
    $order_de_compra =  $row[6];
    $esta_vigente =  $row[7] == 'SI' ? true : false;
    $moneda =  $row[8];

    // Obtener el id de la tabla operador usando id_operador
    $query = "SELECT id FROM operador WHERE id_operador = '$id_operador'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row_operador = mysqli_fetch_assoc($result);
        $id = $row_operador['id'];

        // Insertar los datos en la tabla cliente_uso
        $sql = "INSERT INTO cliente_uso (id_operador, id_servicio_orbyta, cliente, direccion, capacidad, order_de_compra, esta_vigente, moneda) 
                VALUES ('$id', '$id_servicio_orbyta', '$cliente_uso', '$direccion', '$capacidad', '$order_de_compra', '$esta_vigente', '$moneda')";

        if (mysqli_query($conn, $sql)) {
           
        } else {
            $error = 1;
        }
    } else {
        echo "Error: operador with id_operador '$id_operador' not found\n";
    }
}

fclose($file);


if($error == 0){
    echo "<img src='https://gifdb.com/images/high/animated-celebration-happy-cat-confetti-hh6i0vvrbf7wufi0.gif' alt='GIF'>";
}else{
    echo "<img src='https://media.tenor.com/eicSbqkXDFIAAAAM/byuntear-react.gif' alt='GIF'>";
    // Cerrar conexión
}
mysqli_close($conn);
?>

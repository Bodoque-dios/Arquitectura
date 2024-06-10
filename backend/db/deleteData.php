<?php

// if it was a get we die

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    die();
}

require_once '../config.php';

$queries = array(
    "DELETE FROM operador",
    "ALTER TABLE operador AUTO_INCREMENT = 1",
    "DELETE FROM servicio",
    "ALTER TABLE servicio AUTO_INCREMENT = 1",
    "DELETE FROM cargo_mensual",
    "ALTER TABLE cargo_mensual AUTO_INCREMENT = 1",
);

foreach ($queries as $query) {
    mysqli_query($conn, $query);
}

//redirect to index
header('Location: ../db');
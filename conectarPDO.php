<?php

function dbConnect() {
    $servidor = 'localhost';
    $base = 'bdbanco';
    $usuario = 'xestor';
    $contrasinal = 'abc123';
    $dsn="mysql:host=$servidor;dbname=$base";
    $opt= [
        PDO::MYSQL_ATTR_INIT_COMMAND =>"SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    try {
        $db = new PDO($dsn,$usuario,$contrasinal,$opt);
    } catch (PDOException $e) {
        echo '<p>No conectado !!</p>';
        echo $e->getMessage();
        exit;
    }
    return $db;
}
?>


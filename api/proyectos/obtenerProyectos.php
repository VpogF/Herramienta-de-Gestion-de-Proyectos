<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

$id_user = $_SESSION["user_id"];
// $id_user = "2";

$conexion = openBd();

$sentenciaText = "SELECT 
            p.id_proyecto, 
            p.nom_proyecto
        FROM 
            tener t
        JOIN 
            proyecto p ON t.id_proyecto = p.id_proyecto
        WHERE 
            t.id_user = :id_user";

$sentencia = $conexion->prepare($sentenciaText);
$sentencia->bindParam(':id_user', $id_user);
$sentencia->execute();
$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultado);

?>
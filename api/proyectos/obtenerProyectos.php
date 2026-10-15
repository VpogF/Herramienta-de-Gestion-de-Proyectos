<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

$id_user = $_SESSION["user_id"];
// $id_user = "2";

$conexion = openBd();

// id_estado 3 = "Hecha"
$sentenciaText = "SELECT
            p.id_proyecto,
            p.nom_proyecto,
            (SELECT COUNT(*) FROM tarea ta WHERE ta.id_proyecto = p.id_proyecto) AS total_tareas,
            (SELECT COUNT(*) FROM tarea ta WHERE ta.id_proyecto = p.id_proyecto AND ta.id_estado = 3) AS tareas_hechas,
            (SELECT COUNT(*) FROM tener te WHERE te.id_proyecto = p.id_proyecto) AS total_miembros
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
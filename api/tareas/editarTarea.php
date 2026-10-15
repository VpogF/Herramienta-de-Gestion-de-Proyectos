<?php
session_start();
require_once("../../php_librarys/bd.php");

header('Content-Type: application/json');

// Recibe {"id_tarea", "nom_tarea", "descripcion", "id_user", "id_tipo", "fecha_ini", "fecha_fin"}
// y devuelve la tarea actualizada con los mismos campos que obtenerTareas.php
function responder($codigo, $datos)
{
    http_response_code($codigo);
    echo json_encode($datos);
    exit();
}

function fechaValida($texto)
{
    $fecha = DateTime::createFromFormat('Y-m-d', $texto);
    return $fecha && $fecha->format('Y-m-d') === $texto;
}

$datos = json_decode(file_get_contents('php://input'), true);

$id_user_sesion = $_SESSION["user_id"] ?? null;
$id_proyecto = $_SESSION["id_proyecto"] ?? null;
if (!$id_user_sesion || !$id_proyecto || !is_array($datos)) {
    responder(400, ['ok' => false, 'error' => 'Datos incompletos']);
}

$id_tarea = (int) ($datos['id_tarea'] ?? 0);
$nom_tarea = trim((string) ($datos['nom_tarea'] ?? ''));
$descripcion = trim((string) ($datos['descripcion'] ?? ''));
$id_tipo = (int) ($datos['id_tipo'] ?? 0);
// Campos opcionales vacíos -> NULL
$id_encargado = ($datos['id_user'] ?? '') !== '' ? (int) $datos['id_user'] : null;
$fecha_ini = ($datos['fecha_ini'] ?? '') !== '' ? $datos['fecha_ini'] : null;
$fecha_fin = ($datos['fecha_fin'] ?? '') !== '' ? $datos['fecha_fin'] : null;

// Validaciones (mismos límites que las columnas de la tabla tarea)
if ($id_tarea <= 0 || $id_tipo <= 0) {
    responder(400, ['ok' => false, 'error' => 'Datos incompletos']);
}
if ($nom_tarea === '') {
    responder(422, ['ok' => false, 'error' => 'La tarea necesita un nombre.']);
}
if (mb_strlen($nom_tarea) > 100) {
    responder(422, ['ok' => false, 'error' => 'El nombre no puede superar los 100 caracteres.']);
}
if (mb_strlen($descripcion) > 200) {
    responder(422, ['ok' => false, 'error' => 'La descripción no puede superar los 200 caracteres.']);
}
if (($fecha_ini !== null && !fechaValida($fecha_ini)) || ($fecha_fin !== null && !fechaValida($fecha_fin))) {
    responder(422, ['ok' => false, 'error' => 'Alguna de las fechas no es válida.']);
}
if ($fecha_ini !== null && $fecha_fin !== null && $fecha_fin < $fecha_ini) {
    responder(422, ['ok' => false, 'error' => 'La fecha de fin no puede ser anterior a la de inicio.']);
}

$conexion = openBd();

// El usuario debe ser miembro del proyecto y la tarea pertenecer a él
$permiso = $conexion->prepare("select 1 from tarea
    where tarea.id_tarea = :id_tarea and tarea.id_proyecto = :id_proyecto
      and exists (select 1 from tener where tener.id_proyecto = :id_proyecto2 and tener.id_user = :id_user)");
$permiso->execute([
    ':id_tarea' => $id_tarea,
    ':id_proyecto' => $id_proyecto,
    ':id_proyecto2' => $id_proyecto,
    ':id_user' => $id_user_sesion,
]);
if (!$permiso->fetch()) {
    responder(403, ['ok' => false, 'error' => 'No tienes permiso para editar esta tarea.']);
}

// El tipo debe existir y el encargado (si hay) ser miembro del proyecto
$tipo = $conexion->prepare("select 1 from tipo where id_tipo = :id_tipo");
$tipo->execute([':id_tipo' => $id_tipo]);
if (!$tipo->fetch()) {
    responder(422, ['ok' => false, 'error' => 'El tipo de tarea no existe.']);
}
if ($id_encargado !== null) {
    $miembro = $conexion->prepare("select 1 from tener where id_proyecto = :id_proyecto and id_user = :id_user");
    $miembro->execute([':id_proyecto' => $id_proyecto, ':id_user' => $id_encargado]);
    if (!$miembro->fetch()) {
        responder(422, ['ok' => false, 'error' => 'El encargado debe ser participante del proyecto.']);
    }
}

$actualizar = $conexion->prepare("update tarea set
        nom_tarea = :nom_tarea, descripcion = :descripcion, fecha_ini = :fecha_ini, fecha_fin = :fecha_fin,
        id_tipo = :id_tipo, id_user = :id_user
    where id_tarea = :id_tarea and id_proyecto = :id_proyecto");
$actualizar->execute([
    ':nom_tarea' => $nom_tarea,
    ':descripcion' => $descripcion,
    ':fecha_ini' => $fecha_ini,
    ':fecha_fin' => $fecha_fin,
    ':id_tipo' => $id_tipo,
    ':id_user' => $id_encargado,
    ':id_tarea' => $id_tarea,
    ':id_proyecto' => $id_proyecto,
]);

// Devolvemos la tarea tal y como la entrega obtenerTareas.php, para repintar la tarjeta
$consulta = $conexion->prepare("select tarea.id_tarea, tarea.nom_tarea, tarea.descripcion, tarea.fecha_ini, tarea.fecha_fin,
    tarea.id_estado, estado.nom_estado, tarea.id_tipo, tipo.nom_tipo, tarea.id_user, usuario.nom_user
from tarea
inner join estado on tarea.id_estado = estado.id_estado
inner join tipo on tarea.id_tipo = tipo.id_tipo
left join usuario on tarea.id_user = usuario.id_user
where tarea.id_tarea = :id_tarea");
$consulta->execute([':id_tarea' => $id_tarea]);

echo json_encode(['ok' => true, 'tarea' => $consulta->fetch(PDO::FETCH_ASSOC)]);

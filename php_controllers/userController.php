<?php
session_start();
require_once('../php_librarys/bd.php');

// $_SESSION['nom_proyecto'] = $_POST['nom_proyecto'];

if (isset($_POST['insert'])) {
    insertUser($_POST['nom_user'], $_POST['email_user'], $_POST['pass_user']);

    header('Location: ../inicioSesion.php');
    exit();
}

if (isset($_POST['login'])) {
    $nom_user = $_POST['nom_user'];
    $pass_user = $_POST['pass_user'];

    // Consulta para verificar el usuario
    $user = login($nom_user);

    if ($user && hash('sha256', $pass_user) === $user['pass_user']) {
        // Contraseña válida
        session_start();
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['nom_user'];
        echo "Inicio de sesión exitoso. Bienvenido, " . htmlspecialchars($user['username']) . "!";

        header('Location: ../misProyectos.php');
        exit();

    } else {
        // Usuario o contraseña incorrectos
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header('Location: ../inicioSesion.php');
        exit();

    }
}

if (isset($_POST['crear-proyecto'])) {
    insertProyecto($_POST['nom_proyecto'], $_SESSION['user_id']);


    header('Location: ../misProyectos.php');
    exit();
}

if (isset($_POST['delete-proyecto'])) {

    $_SESSION['id_proyecto'] = $_POST['delete-proyecto'];

    deleteProyecto($_POST['id_proyecto']);
    header('Location: ../misProyectos.php');
    exit();
}

if (isset($_POST['acceder-proyecto'])) {
 //   seleccionarTareas($_POST['id_proyecto']);
    $_SESSION['id_proyecto'] = $_POST['acceder-proyecto'];
    $_SESSION["nom_proyecto"] = $_POST['nom_proyecto'];

    header('Location: ../misTareas.php');
    exit();
}

if (isset($_POST['crear-tarea'])) {
    insertarTarea(
        $_POST['nom_tarea'],
        $_POST['descripcion'],
        $_POST['fecha-ini'],
        $_POST['fecha-fin'],
        $_SESSION['id_proyecto'],
        $_POST['tipo'],
        $_POST['id_estado'],
        $_POST['encargado']
    );

    header('Location: ../misTareas.php');
    exit();
}

if (isset($_POST['delete-tarea'])) {

    $_SESSION['id_tarea'] = $_POST['id_tarea'];

    deleteTarea($_POST['id_tarea']);
    header('Location: ../misTareas.php');
    exit();
}

// if (isset($_POST['aniadir-usuario'])) {
//     $_SESSION['id_proyecto'] = $_POST['aniadir-usuario'];
//     $_SESSION['nom_proyecto'] = $_POST['nom_proyecto'];
//     header('Location: ../misProyectos.php');

    
// }

if (isset($_POST['aniadir-col'])) {

    agregarColaboradorTener($_POST['colaborador'], $_POST['idProyecto']);
    header('Location: ../misProyectos.php');
    exit();
}




?>
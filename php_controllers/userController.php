<?php
session_start();
require_once('../php_librarys/bd.php');

// $_SESSION['nom_proyecto'] = $_POST['nom_proyecto'];

if (isset($_POST['insert'])) {
    $nom_user = trim($_POST['nom_user'] ?? '');
    $email_user = trim($_POST['email_user'] ?? '');
    $pass_user = $_POST['pass_user'] ?? '';
    $pass_user2 = $_POST['pass_user2'] ?? '';

    $error = null;
    if ($nom_user === '' || $email_user === '' || $pass_user === '') {
        $error = "Rellena todos los campos.";
    } elseif (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no es válido.";
    } elseif (strlen($pass_user) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($pass_user !== $pass_user2) {
        $error = "Las contraseñas no coinciden.";
    } elseif (login($nom_user)) {
        // nom_user es UNIQUE: sin esta comprobación el insert lanza una excepción
        $error = "Ese nombre de usuario ya existe. Prueba con otro.";
    }

    if ($error) {
        $_SESSION['error_registro'] = $error;
        $_SESSION['registro_nom_user'] = $nom_user;
        $_SESSION['registro_email_user'] = $email_user;
        header('Location: ../registro.php');
        exit();
    }

    insertUser($nom_user, $email_user, $pass_user);

    $_SESSION['exito'] = "Cuenta creada. Ya puedes iniciar sesión.";
    $_SESSION['login_nom_user'] = $nom_user;
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
        $_SESSION['login_nom_user'] = $nom_user;
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
    // Campos opcionales vacíos -> NULL (MySQL no acepta '' en columnas DATE/INT)
    $fecha_ini = ($_POST['fecha-ini'] ?? '') !== '' ? $_POST['fecha-ini'] : null;
    $fecha_fin = ($_POST['fecha-fin'] ?? '') !== '' ? $_POST['fecha-fin'] : null;
    $encargado = ($_POST['encargado'] ?? '') !== '' ? $_POST['encargado'] : null;

    insertarTarea(
        trim($_POST['nom_tarea']),
        trim($_POST['descripcion'] ?? ''),
        $fecha_ini,
        $fecha_fin,
        $_SESSION['id_proyecto'],
        $_POST['tipo'],
        $_POST['id_estado'],
        $encargado
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
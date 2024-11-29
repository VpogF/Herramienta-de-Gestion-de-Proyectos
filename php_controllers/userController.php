<?php 
    session_start();
    require_once('../php_librarys/bd.php');

    if (isset($_POST['insert']))
    {
        insertUser($_POST['nom_user'], $_POST['email_user'], $_POST['pass_user']);

        header('Location: ../inicioSesion.php');
        exit();
    }

    if (isset($_POST['login']))
    {
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

            header('Location: ../misProyectos.html');
            exit();

        } else {
            // Usuario o contraseña incorrectos
            $_SESSION['error'] = "Usuario o contraseña incorrectos.";
            header('Location: ../inicioSesion.php');
            exit();

        }
        }

?>
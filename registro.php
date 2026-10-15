<?php
    session_start();
    require_once('./php_librarys/bd.php');

    // Si ya hay sesión iniciada, no tiene sentido mostrar el registro
    if (isset($_SESSION['user_id'])) {
        header('Location: ./misProyectos.php');
        exit();
    }

    $error = isset($_SESSION['error_registro']) ? $_SESSION['error_registro'] : null;
    $nomAnterior = isset($_SESSION['registro_nom_user']) ? $_SESSION['registro_nom_user'] : '';
    $emailAnterior = isset($_SESSION['registro_email_user']) ? $_SESSION['registro_email_user'] : '';
    unset($_SESSION['error_registro'], $_SESSION['registro_nom_user'], $_SESSION['registro_email_user']);
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta · HerramientaGP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css?v=<?php echo filemtime(__DIR__ . "/style.css"); ?>">
    <link rel="stylesheet" href="./myStyle.css?v=<?php echo filemtime(__DIR__ . "/myStyle.css"); ?>">
</head>

<body class="auth-body">
    <nav class="navbar navbar-light bg-primary auth-nav">
        <div class="container-fluid justify-content-center">
            <a class="navbar-brand m-0" href="./index.html" aria-label="Volver a la página principal">
                <img src="./img/logo_3-blanco.jpg" alt="Logo de HerramientaGP" width="50" height="50"
                    class="rounded-circle">
            </a>
        </div>
    </nav>

    <main class="auth-page">
        <div class="auth-card">
            <header class="auth-header">
                <h1 class="auth-title">Crear cuenta</h1>
                <p class="auth-subtitle">Empieza a organizar tus proyectos</p>
            </header>

            <form class="auth-form" id="formRegistro" action="./php_controllers/userController.php" method="POST">

                <?php if ($error) { ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php } ?>

                <div class="mb-3">
                    <label for="nom_user" class="form-label auth-label">Nombre de usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control auth-input" name="nom_user" id="nom_user"
                            value="<?php echo htmlspecialchars($nomAnterior); ?>" maxlength="50"
                            autocomplete="username" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email_user" class="form-label auth-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control auth-input" name="email_user" id="email_user"
                            value="<?php echo htmlspecialchars($emailAnterior); ?>" maxlength="50"
                            placeholder="nombre@ejemplo.com" autocomplete="email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="pass_user" class="form-label auth-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control auth-input" name="pass_user" id="pass_user"
                            minlength="6" autocomplete="new-password" aria-describedby="passAyuda" required>
                        <button type="button" class="btn auth-toggle-pass" data-toggle-pass="pass_user"
                            aria-label="Mostrar contraseña" title="Mostrar contraseña">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div id="passAyuda" class="auth-help">Mínimo 6 caracteres.</div>
                </div>

                <div class="mb-4">
                    <label for="pass_user2" class="form-label auth-label">Repite la contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control auth-input" name="pass_user2" id="pass_user2"
                            minlength="6" autocomplete="new-password" required>
                        <button type="button" class="btn auth-toggle-pass" data-toggle-pass="pass_user2"
                            aria-label="Mostrar contraseña" title="Mostrar contraseña">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 auth-submit" name="insert">Crear cuenta</button>
            </form>

            <p class="auth-footer">
                ¿Ya tienes cuenta? <a href="./inicioSesion.php">Inicia sesión</a>
            </p>
        </div>
    </main>


    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        // Mostrar / ocultar contraseña
        document.querySelectorAll('[data-toggle-pass]').forEach(boton => {
            const input = document.getElementById(boton.dataset.togglePass);
            boton.addEventListener('click', () => {
                const oculta = input.type === 'password';
                input.type = oculta ? 'text' : 'password';
                const texto = oculta ? 'Ocultar contraseña' : 'Mostrar contraseña';
                boton.setAttribute('aria-label', texto);
                boton.title = texto;
                boton.querySelector('i').className = oculta ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        });

        // Avisar antes de enviar si las contraseñas no coinciden
        const pass1 = document.getElementById('pass_user');
        const pass2 = document.getElementById('pass_user2');
        function comprobarCoincidencia() {
            pass2.setCustomValidity(pass2.value && pass1.value !== pass2.value ? 'Las contraseñas no coinciden.' : '');
        }
        pass1.addEventListener('input', comprobarCoincidencia);
        pass2.addEventListener('input', comprobarCoincidencia);
    </script>
</body>



</html>

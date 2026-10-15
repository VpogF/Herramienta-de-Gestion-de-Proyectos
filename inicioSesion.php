<?php
    session_start();
    require_once('./php_librarys/bd.php');

    // Si ya hay sesión iniciada, no tiene sentido mostrar el login
    if (isset($_SESSION['user_id'])) {
        header('Location: ./misProyectos.php');
        exit();
    }

    $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
    $exito = isset($_SESSION['exito']) ? $_SESSION['exito'] : null;
    $nomAnterior = isset($_SESSION['login_nom_user']) ? $_SESSION['login_nom_user'] : '';
    unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['login_nom_user']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · HerramientaGP</title>
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
                <h1 class="auth-title">Iniciar sesión</h1>
                <p class="auth-subtitle">Te damos la bienvenida de nuevo</p>
            </header>

            <form class="auth-form" action="./php_controllers/userController.php" method="POST">

                <?php if ($error) { ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php } ?>

                <?php if ($exito) { ?>
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2" role="status">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?php echo htmlspecialchars($exito); ?></span>
                    </div>
                <?php } ?>

                <div class="mb-3">
                    <label for="nom_user" class="form-label auth-label">Nombre de usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control auth-input" name="nom_user" id="nom_user"
                            value="<?php echo htmlspecialchars($nomAnterior); ?>" autocomplete="username"
                            required <?php echo $nomAnterior === '' ? 'autofocus' : ''; ?>>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="pass_user" class="form-label auth-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control auth-input" name="pass_user" id="pass_user"
                            autocomplete="current-password" required <?php echo $nomAnterior !== '' ? 'autofocus' : ''; ?>>
                        <button type="button" class="btn auth-toggle-pass" id="togglePass"
                            aria-label="Mostrar contraseña" title="Mostrar contraseña">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 auth-submit" name="login">Ingresar</button>
            </form>

            <p class="auth-footer">
                ¿No tienes cuenta? <a href="./registro.php">Regístrate</a>
            </p>
        </div>
    </main>


    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        // Mostrar / ocultar contraseña
        const togglePass = document.getElementById('togglePass');
        const passInput = document.getElementById('pass_user');
        togglePass.addEventListener('click', () => {
            const oculta = passInput.type === 'password';
            passInput.type = oculta ? 'text' : 'password';
            const texto = oculta ? 'Ocultar contraseña' : 'Mostrar contraseña';
            togglePass.setAttribute('aria-label', texto);
            togglePass.title = texto;
            togglePass.querySelector('i').className = oculta ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    </script>
</body>



</html>

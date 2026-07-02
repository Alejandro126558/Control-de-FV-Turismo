<?php
session_start();
// Si ya existe una sesión activa, puedes redirigir al panel directamente
if (isset($_SESSION['usuario'])) {
    header("Location: admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlotaTurismo | Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css?v=1.3">

    <style>
        /* Estilos específicos y optimizaciones estructurales para la pantalla de Login */
        body {
            padding-top: 0;
            /* Eliminamos el espacio del navbar para centrar el login */
            background-color: var(--bg-light);
        }

        .login-container {
            min-height: 100vh;
        }

        .login-side {
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 10px 0 40px rgba(15, 23, 42, 0.03);
            z-index: 2;
        }

        /* Modificado para usar tu imagen real eliminando el código vectorial antiguo */
        .image-side {
            background-image: url('img/login_fleet.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Capa translúcida sobre la imagen para mantener el balance y legibilidad del texto */
        .text-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(240, 253, 244, 0.5) 0%, rgba(248, 250, 252, 0.4) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .text-overlay > div {
            z-index: 2;
        }

        .form-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }

        .brand-logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--secondary);
            text-decoration: none;
        }

        .brand-logo span {
            color: var(--primary);
        }

        .input-group-text {
            background-color: #f8fafc;
            border-right: none;
            color: #64748b;
        }

        .form-control {
            border-left: none;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.65rem 1rem;
        }

        .form-control:focus {
            border-color: #cbd5e1;
            box-shadow: none;
            background-color: #fff;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            color: var(--primary);
        }

        .input-group:focus-within .form-control {
            border-color: var(--primary);
        }
    </style>
</head>

<body>

    <div class="container-fluid open-sans">
        <div class="row login-container">

            <div class="col-lg-5 col-xl-4 login-side">
                <div class="form-wrapper">

                    <div class="text-center mb-5">
                        <a href="index.php" class="brand-logo">
                            <i class="fa-solid fa-satellite-dish me-2"></i>Flota<span>Turismo</span>
                        </a>
                        <p class="text-muted small mt-2">Plataforma Inteligente de Telemetría Vehicular</p>
                    </div>

                    <h2 class="fw-bold text-secondary mb-1" style="letter-spacing: -0.02em;">Bienvenido</h2>
                    <p class="text-muted small mb-4">Ingrese sus credenciales para acceder al centro de control.</p>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-4 rounded-3 small" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i>
                            <div>Correo o contraseña incorrectos.</div>
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" action="controlador/login_process.php" method="POST" autocomplete="off" novalidate>

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-secondary">Correo Electrónico</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="ejemplo@empresa.com" required autofocus>
                                <div id="error-email" class="invalid-feedback fw-semibold"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label small fw-bold text-secondary mb-0">Contraseña</label>
                                <a href="#" class="text-decoration-none small fw-semibold text-primary">¿La olvidó?</a>
                            </div>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="••••••••" required>
                                <div id="error-password" class="invalid-feedback fw-semibold"></div>
                            </div>
                        </div>

                        <div class="form-check mb-4 small">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-input-label text-muted fw-medium" for="remember">
                                Mantener sesión iniciada
                            </label>
                        </div>

                        <button type="submit" id="btnSubmit" class="btn btn-panel w-100 py-2.5 fw-bold mb-3">
                            Ingresar al Sistema <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                        </button>

                        <div class="text-center mb-4">
                            <span class="text-muted small">¿Cliente nuevo?</span>
                            <a href="register.php" class="text-decoration-none small fw-bold text-primary">Regístrese</a>
                        </div>

                        <div class="text-center">
                            <a href="index.php" class="text-decoration-none text-muted small fw-semibold">
                                <i class="fa-solid fa-arrow-left me-1"></i> Volver al sitio principal
                            </a>
                        </div>
                    </form>

                </div>
            </div>

            <div class="col-lg-7 col-xl-8 d-none d-lg-flex image-side">
                <div class="text-overlay">
                    <div class="text-center p-5 bg-white bg-opacity-75 rounded-4 shadow-sm border border-white" style="max-width: 540px; backdrop-filter: blur(8px);">
                        <div class="icon-shape bg-soft-primary mx-auto mb-3" style="width: 64px; height: 64px; border-radius: 18px;">
                            <i class="fa-solid fa-route fa-xl"></i>
                        </div>
                        <h4 class="fw-bold text-secondary">Trazabilidad de Rutas en un Solo Lugar</h4>
                        <p class="text-muted small mb-0">Monitoree velocidades, asigne conductores a itinerarios de forma segura y mantenga el control completo de su flota turística local y nacional.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="js/login.js"></script>
</body>

</html>
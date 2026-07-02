<?php
session_start();

// Evitar almacenamiento en caché (Heredado de la arquitectura de compras-master)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Conexión real a la base de datos fv_turismo
require_once 'conexion/config.php';

// ️ Conexión a BD
$db = new Database();
$pdo = $db->conectar();
if (!$pdo) {
    die('<div class="alert alert-danger text-center mt-5"> Error de conexión a la base de datos</div>');
}

try {
    // 1. Telemetría y Métricas en tiempo real
    $stmtActivos = $pdo->query("SELECT COUNT(*) FROM vehiculos WHERE estado = 'Disponible'");
    $vehiculos_activos = $stmtActivos->fetchColumn();

    $stmtAlertas = $pdo->query("SELECT COUNT(*) FROM novedades WHERE FECHA = CURDATE()"); 
    $alertas_velocidad = $stmtAlertas->fetchColumn();

    $stmtServicios = $pdo->query("SELECT COUNT(*) FROM servicios WHERE fecha_servicio = CURDATE()");
    $servicios_hoy = $stmtServicios->fetchColumn();

    // 2. Consulta dinámica de la tabla de la Flota Vehicular
    $stmtFlota = $pdo->query("SELECT placa, marca, modelo, tipo_vehiculo, capacidad, estado FROM vehiculos LIMIT 5");
    $lista_vehiculos = $stmtFlota->fetchAll();

} catch (PDOException $e) {
    $vehiculos_activos = 0;
    $alertas_velocidad = 0;
    $servicios_hoy = 0;
    $lista_vehiculos = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlotaTurismo | Sistema de Gestión y Telemetría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Estilos de soporte para garantizar contraste en enlaces del footer */
        .footer-corporate a {
            color: #94a3b8 !important;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .footer-corporate a:hover {
            color: #0284c7 !important;
        }
        .footer-corporate .social-icon {
            width: 38px;
            height: 38px;
            background-color: rgba(255, 255, 255, 0.08);
            color: #ffffff !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .footer-corporate .social-icon:hover {
            background-color: #0284c7;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-xl fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-satellite-dish me-2"></i><span>Flota</span>Turismo
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFlota">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarFlota">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#servicios">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#flota">Flota</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rutas">Rutas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#reservas">Reservas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#nosotros">Nosotros</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['tip_user'])): ?>
                        <a href="admin/index.php" class="btn btn-primary btn-panel px-4">
                            <i class="fas fa-chart-line me-2"></i>Panel Operativo
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary btn-login px-4">
                            <i class="fas fa-user-lock me-2"></i>Iniciar Sesión
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <section id="inicio" class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-soft-success text-success px-3 py-2 mb-3 fw-bold rounded-pill">
                        <i class="fas fa-shield-halved me-2"></i>LOGÍSTICA SEGURA E HÍBRIDA
                    </span>
                    <h1 class="display-5 hero-title mb-3">Control Total de su Flota Turística en Tiempo Real</h1>
                    <p class="lead text-muted mb-4">Elimine el registro manual. Nuestro ecosistema centraliza la trazabilidad de rutas, control de velocidades por GPS y planes de mantenimiento preventivo.</p>
                    <div class="d-flex gap-3">
                        <a href="#servicios" class="btn btn-primary btn-lg px-4">Explorar Soluciones</a>
                        <a href="#reservas" class="btn btn-reserva-hero btn-lg px-4">Ver Reservas</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="telematics-card p-4 shadow-lg">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="pulse-indicator"></span>
                                <h6 class="text-muted mb-0 small uppercase font-monospace">Consola Satelital Activa</h6>
                            </div>
                            <span class="badge bg-dark text-success border border-success font-monospace">GPS ONLINE</span>
                        </div>
                        <div class="map-container rounded mb-3 position-relative overflow-hidden">
                            <img src="img/map-dashboard.svg" alt="Centro de Control de Rutas" class="img-fluid w-100 h-100 object-fit-cover">
                            <div class="map-overlay-card p-2 rounded shadow-sm">
                                <p class="mb-0 text-dark fw-bold" style="font-size: 0.75rem;"><i class="fas fa-bus text-success me-1"></i> Van-04 (Ruta Ibagué - Bogotá)</p>
                                <span class="text-muted font-monospace" style="font-size: 0.65rem;">Velocidad: 78 km/h - Normal</span>
                            </div>
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="metric-box p-2 rounded">
                                    <h5 class="text-success fw-bold mb-0" id="count-active"><?= htmlspecialchars($vehiculos_activos) ?></h5>
                                    <small class="text-muted d-block" style="font-size:0.7rem;">Vehículos Disponibles</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="metric-box p-2 rounded">
                                    <h5 class="text-danger fw-bold mb-0" id="count-alerts"><?= htmlspecialchars($alertas_velocidad) ?></h5>
                                    <small class="text-muted d-block" style="font-size:0.7rem;">Alertas / Novedades</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="metric-box p-2 rounded">
                                    <h5 class="text-primary fw-bold mb-0"><?= htmlspecialchars($servicios_hoy) ?></h5>
                                    <small class="text-muted d-block" style="font-size:0.7rem;">Itinerarios Hoy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="servicios" class="py-5">
        <div class="container py-4">
            <div class="text-center max-width-700 mx-auto mb-5">
                <h2 class="fw-bold text-secondary">Módulos Operacionales Integrados</h2>
                <p class="text-muted">Desarrollado para mitigar los riesgos logísticos identificados mediante la centralización tecnológica de datos.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card service-card h-100 p-4 border-0 shadow-sm">
                        <div class="icon-shape bg-soft-primary text-primary mb-3">
                            <i class="fas fa-location-crosshairs fa-lg"></i>
                        </div>
                        <h5 class="fw-bold text-secondary">Monitoreo GPS Continuo</h5>
                        <p class="text-muted small mb-0">Localización exacta de vans, buses y automóviles asignados a transfers hoteleros y excursiones con actualizaciones constantes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card h-100 p-4 border-0 shadow-sm">
                        <div class="icon-shape bg-soft-danger text-danger mb-3">
                            <i class="fas fa-gauge-high fa-lg"></i>
                        </div>
                        <h5 class="fw-bold text-secondary">Alertas de Conducción</h5>
                        <p class="text-muted small mb-0">Control predictivo que genera notificaciones inmediatas ante excesos de velocidad, protegiendo la integridad del turista.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card h-100 p-4 border-0 shadow-sm">
                        <div class="icon-shape bg-soft-success text-success mb-3">
                            <i class="fas fa-gas-pump fa-lg"></i>
                        </div>
                        <h5 class="fw-bold text-secondary">Combustible y Taller</h5>
                        <p class="text-muted small mb-0">Mapeo del rendimiento de combustible por trayecto y alertas automáticas basadas en kilometraje para mantenimientos preventivos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="flota" class="py-5 bg-white border-top border-bottom">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5">
                    <h2 class="fw-bold text-secondary mb-3">Administración de Unidades Vehiculares</h2>
                    <p class="text-muted">Clasifique y controle los vehículos autorizados en la plataforma según sus marcas, capacidades de pasajeros y estados técnicos mecánicos en tiempo real.</p>
                    <ul class="list-unstyled space-y-3 text-muted">
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Vinculación por marcas oficiales y tipos homologados.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Trazabilidad completa por número de Placa Única.</li>
                        <li><i class="fas fa-check-circle text-primary me-2"></i> Vinculación directa con el propietario o empresa operadora.</li>
                    </ul>
                </div>
                <div class="col-lg-7">
                    <div class="table-responsive shadow-sm border rounded">
                        <table class="table table-hover align-middle mb-0 bg-white">
                            <thead class="table-dark-custom text-white" style="background-color: #212529;">
                                <tr>
                                    <th>Placa</th>
                                    <th>Marca / Modelo</th>
                                    <th>Tipo</th>
                                    <th>Capacidad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody class="font-monospace small">
                                <?php if (!empty($lista_vehiculos)): ?>
                                    <?php foreach ($lista_vehiculos as $vehiculo): 
                                        $badge_class = 'bg-success';
                                        if ($vehiculo['estado'] === 'En Taller' || $vehiculo['estado'] === 'Mantenimiento') {
                                            $badge_class = 'bg-warning text-dark';
                                        } elseif ($vehiculo['estado'] === 'Inactivo') {
                                            $badge_class = 'bg-danger';
                                        }
                                    ?>
                                        <tr>
                                            <td class="fw-bold text-primary"><?= htmlspecialchars($vehiculo['placa']) ?></td>
                                            <td><?= htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']) ?></td>
                                            <td><?= htmlspecialchars($vehiculo['tipo_vehiculo']) ?></td>
                                            <td><?= htmlspecialchars($vehiculo['capacidad']) ?> Pasajeros</td>
                                            <td><span class="badge <?= $badge_class ?>"><?= htmlspecialchars($vehiculo['estado']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No hay unidades vehiculares registradas en la base de datos o falló la conexión.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="rutas" class="py-5 bg-mint-section">
        <div class="container">
            <div class="text-center max-width-700 mx-auto mb-5">
                <h2 class="fw-bold text-secondary">Trazabilidad de Destinos Turísticos</h2>
                <p class="text-muted">Planificación inteligente de despachos evitando la superposición horaria o colisiones de agenda en la flota.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="route-card bg-white p-4 rounded border shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-soft-primary text-primary px-2.5 py-1 rounded">Intermunicipal</span>
                            <span class="text-muted small"><i class="far fa-clock me-1"></i> 3.5 Horas</span>
                        </div>
                        <h6 class="fw-bold text-secondary mb-1">Ruta del Sol - Ecoturismo</h6>
                        <p class="text-muted small mb-3">Ibagué &rarr; Murillo &rarr; Parque Los Nevados</p>
                        <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Asignado a: <strong>Coordinador Norte</strong></span>
                            <i class="fas fa-chevron-right text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="route-card bg-white p-4 rounded border shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-soft-success text-success px-2.5 py-1 rounded">Transfer Urbano</span>
                            <span class="text-muted small"><i class="far fa-clock me-1"></i> 45 Mins</span>
                        </div>
                        <h6 class="fw-bold text-secondary mb-1">Eje Corporativo Ejecutivo</h6>
                        <p class="text-muted small mb-3">Hoteles Zona Centro &rarr; Aeropuerto Perales</p>
                        <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Asignado a: <strong>Vendedor VIP</strong></span>
                            <i class="fas fa-chevron-right text-primary"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mx-auto">
                    <div class="route-card bg-white p-4 rounded border shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-soft-warning text-warning px-2.5 py-1 rounded">Especial Histórico</span>
                            <span class="text-muted small"><i class="far fa-clock me-1"></i> 1.5 Horas</span>
                        </div>
                        <h6 class="fw-bold text-secondary mb-1">Ruta de los Conquistadores</h6>
                        <p class="text-muted small mb-3">Terminal Ibagué &rarr; Complejo Histórico Ambalema</p>
                        <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Asignado a: <strong>Guía Líder</strong></span>
                            <i class="fas fa-chevron-right text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="reservas" class="py-5 bg-light border-top">
        <div id="nosotros" class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <span class="text-primary font-monospace fw-bold uppercase tracking-wider d-block mb-2">Estructura Organizacional</span>
                    <h3 class="fw-bold text-secondary mb-3">Perfiles y Roles de Seguridad</h3>
                    <p class="text-muted">Cumpliendo estrictamente la documentación técnica desarrollada, el sistema restringe accesos específicos mediante pasarelas criptográficas según el cargo corporativo:</p>
                    <div class="accordion accordion-flush bg-transparent" id="accordionRoles">
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button bg-transparent fw-bold text-secondary px-0" type="button" data-bs-toggle="collapse" data-bs-target="#role1">
                                    <i class="fas fa-user-shield text-primary me-2"></i> Administrador del Sistema
                                </button>
                            </h2>
                            <div id="role1" class="accordion-collapse collapse show" data-bs-parent="#accordionRoles">
                                <div class="accordion-body px-0 text-muted small">
                                    Control total perimetral. Gestión de usuarios, parametrización de vehículos, auditoría de bitácoras de combustible y borrado lógico de registros colisionados.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent fw-bold text-secondary px-0" type="button" data-bs-toggle="collapse" data-bs-target="#role2">
                                    <i class="fas fa-id-card-clip text-primary me-2"></i> Conductores / Choferes
                                </button>
                            </h2>
                            <div id="role2" class="accordion-collapse collapse" data-bs-parent="#accordionRoles">
                                <div class="accordion-body px-0 text-muted small">
                                    Visualización de hojas de ruta asignadas, reporte rápido de alertas de incidentes viales y marcación digital de inicio/fin de itinerario turístico.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent fw-bold text-secondary px-0" type="button" data-bs-toggle="collapse" data-bs-target="#role3">
                                    <i class="fas fa-screwdriver-wrench text-primary me-2"></i> Técnicos de Taller
                                </button>
                            </h2>
                            <div id="role3" class="accordion-collapse collapse" data-bs-parent="#accordionRoles">
                                <div class="accordion-body px-0 text-muted small">
                                    Gestión de órdenes de servicio técnico preventivo o correctivo, actualización de inventario de repuestos y liberación de vehículos en mantenimiento.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card p-4 border-0 shadow-sm rounded-4 bg-white">
                        <h5 class="fw-bold text-secondary mb-3"><i class="fas fa-calendar-days text-primary me-2"></i>Simulador de Despacho Inmediato</h5>
                        <p class="text-muted small">Módulo rápido para coordinadores turísticos de guardia.</p>
                        <form id="formSimulador" onsubmit="event.preventDefault(); procesarSimulacion();">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Seleccionar Vehículo Activo</label>
                                <select class="form-select bg-light border-0" id="sim-vehiculo">
                                    <option>Toyota Hiace [TXZ-789]</option>
                                    <option>Scania Macropack [SUK-456]</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Destino Turístico</label>
                                <input type="text" class="form-control bg-light border-0" value="Parque Los Nevados" id="sim-destino">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 fw-bold">Despachar Itinerario Vía Satélite</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-corporate text-white pt-5 pb-4" style="background-color: #0f172a; border-top: 4px solid #0284c7;">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <h5 class="fw-bold text-white mb-3"><i class="fas fa-satellite-dish text-primary me-2"></i>FlotaTurismo</h5>
                    <p class="text-white-50 small pe-lg-5">Plataforma dedicada a la analítica de datos vehiculares y telemática avanzada, optimizando la gestión y trazabilidad del transporte turístico masivo y corporativo.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="social-icon" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-3">
                    <h6 class="text-uppercase fw-bold text-primary small mb-3">Navegación</h6>
                    <ul class="list-unstyled footer-links small">
                        <li class="mb-2"><a href="#inicio">Inicio</a></li>
                        <li class="mb-2"><a href="#servicios">Módulos</a></li>
                        <li class="mb-2"><a href="#flota">Control Flota</a></li>
                        <li><a href="#rutas">Mapa Rutas</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4 col-lg-4">
                    <h6 class="text-uppercase fw-bold text-primary small mb-3">Contacto Central</h6>
                    <ul class="list-unstyled text-white-50 small">
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> soporte@flotaturismo.com</li>
                        <li class="mb-2"><i class="fas fa-phone me-2 text-primary"></i> +57 (608) 270-0000</li>
                        <li><i class="fas fa-location-dot me-2 text-primary"></i> Ibagué, Tolima - Colombia</li>
                    </ul>
                </div>
            </div>
            <div class="border-top border-secondary pt-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="mb-0 text-white-50 small">&copy; <?= date('Y') ?> FlotaTurismo Telematics Engine. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function procesarSimulacion() {
            const vehiculo = document.getElementById('sim-vehiculo').value;
            const destino = document.getElementById('sim-destino').value;
            alert(`📡 COMUNICACIÓN SATELITAL ESTABLECIDA:\nUnidad ${vehiculo} ha sido despachada con éxito hacia: ${destino}.\nLas coordenadas GPS y alertas de velocidad han sido activadas.`);

            let numActivos = parseInt(document.getElementById('count-active').innerText);
            document.getElementById('count-active').innerText = numActivos + 1;
        }
    </script>
</body>

</html>
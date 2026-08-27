<?php

declare(strict_types=1);

use App\Controller\ApiController;
use App\Controller\AppointmentController;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\HealthController;
use App\Controller\PatientController;
use App\Core\Database;
use App\Core\Router;
use App\Core\View;
use App\Domain\SlotGenerator;
use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use App\Repository\PatientRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Service\AppointmentService;

// 1. Cargar la configuración
$config = require dirname(__DIR__) . '/bootstrap/app.php';

// 2. Cabeceras de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; base-uri 'self'; frame-ancestors 'none'; form-action 'self'");

try {
    // 3. Conexión a la base de datos
    $database = new Database($config['database']);
    $pdo = $database->pdo();

    // 4. Crear los repositorios
    $users = new UserRepository($pdo);
    $patients = new PatientRepository($pdo);
    $doctors = new DoctorRepository($pdo);
    $rooms = new RoomRepository($pdo);
    $appointments = new AppointmentRepository($pdo);

    // 5. Crear el servicio de citas
    $appointmentService = new AppointmentService(
        $database,
        $appointments,
        $doctors,
        $rooms,
        new SlotGenerator(),
        $config['appointments']
    );

    // 6. Crear los controladores
    $authController = new AuthController($users);
    $dashboardController = new DashboardController($patients, $appointments);
    $patientController = new PatientController($patients);
    $appointmentController = new AppointmentController(
        $patients,
        $doctors,
        $rooms,
        $appointments,
        $appointmentService
    );
    $apiController = new ApiController($appointmentService);
    $healthController = new HealthController($pdo);

    // 7. Crear el enrutador
    $router = new Router($config['base_path']);

    // 8. DEFINIR LAS RUTAS
    $router->get('/login', [$authController, 'showLogin']);   // Muestra el login
    $router->post('/login', [$authController, 'login']);      // Procesa el login
    $router->post('/logout', [$authController, 'logout']);    // Cierra sesión

    // Rutas públicas
    $router->get('/health', [$healthController, 'show']);

    // Dashboard
    $router->get('/', [$dashboardController, 'index']);

    // Pacientes
    $router->get('/patients', [$patientController, 'index']);
    $router->get('/patients/create', [$patientController, 'create']);
    $router->post('/patients', [$patientController, 'store']);

    // Citas
    $router->get('/appointments', [$appointmentController, 'index']);
    $router->get('/appointments/create', [$appointmentController, 'create']);
    $router->post('/appointments', [$appointmentController, 'store']);
    $router->get('/appointments/{id}', [$appointmentController, 'show']);
    $router->post('/appointments/{id}/cancel', [$appointmentController, 'cancel']);
    $router->post('/appointments/{id}/complete', [$appointmentController, 'complete']);

    // API
    $router->get('/api/availability', [$apiController, 'availability']);

    // 9. Despachar la solicitud
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');

} catch (Throwable $exception) {
    error_log((string) $exception);
    http_response_code(500);
    View::render('errors/500', [
        'title' => 'Error del servidor',
        'details' => $config['debug'] ? $exception->getMessage() : null,
    ]);
}
<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'company') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$company = getCompanyByUserId($userId);

if (!$company) {
    header('Location: company_profile.php?new=1');
    exit;
}

$jobs = getJobsByCompanyId($company['id']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Ofertas - Empleate_RD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'includes/header.php'; ?>

    <main class="container py-5 flex-grow-1">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Mi Cuenta</h5>
                        <hr>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="company_profile.php">Perfil de Empresa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="post_job.php">Publicar Oferta</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="manage_jobs.php">Gestionar Ofertas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0">Mis Ofertas de Trabajo</h2>
                            <a href="post_job.php" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Nueva Oferta
                            </a>
                        </div>

                        <?php if ($jobs && count($jobs) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Fecha</th>
                                            <th>Aplicaciones</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jobs as $job): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($job['title']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($job['created_at'])); ?></td>
                                                <td>
                                                    <a href="view_applications.php?job_id=<?php echo $job['id']; ?>"
                                                        class="badge bg-primary text-decoration-none">
                                                        <?php echo $job['applications_count']; ?> aplicaciones
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if ($job['status'] == 'active'): ?>
                                                        <span class="badge bg-success">Activa</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Cerrada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="edit_job.php?id=<?php echo $job['id']; ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="job_details.php?id=<?php echo $job['id']; ?>"
                                                            class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No has publicado ninguna oferta de trabajo aún.
                                <a href="post_job.php" class="alert-link">Publica tu primera oferta</a>.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
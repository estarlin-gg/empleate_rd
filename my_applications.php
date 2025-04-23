<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'candidate') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$candidate = getCandidateByUserId($userId);


if (!$candidate) {
    header('Location: candidate_profile.php?new=1');
    exit;
}


$applications = getApplicationsByCandidateId($candidate['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Aplicaciones - Empleate_RD</title>
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
                                <a class="nav-link" href="candidate_profile.php">Mi Perfil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="my_applications.php">Mis Aplicaciones</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="jobs.php">Buscar Empleos</a>
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
                        <h2 class="mb-4">Mis Aplicaciones</h2>
                        
                        <?php if ($applications && count($applications) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Puesto</th>
                                            <th>Empresa</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $application): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($application['company_logo'])): ?>
                                                            <img src="<?php echo $application['company_logo']; ?>" alt="Logo de empresa" class="me-2" style="width: 30px; height: 30px; object-fit: contain;">
                                                        <?php endif; ?>
                                                        <span><?php echo htmlspecialchars($application['company_name']); ?></span>
                                                    </div>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($application['created_at'])); ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    
                                                    switch ($application['status']) {
                                                        case 'pending':
                                                            $statusClass = 'bg-warning text-dark';
                                                            $statusText = 'Pendiente';
                                                            break;
                                                        case 'reviewed':
                                                            $statusClass = 'bg-info text-dark';
                                                            $statusText = 'Revisado';
                                                            break;
                                                        case 'accepted':
                                                            $statusClass = 'bg-success';
                                                            $statusText = 'Aceptado';
                                                            break;
                                                        case 'rejected':
                                                            $statusClass = 'bg-danger';
                                                            $statusText = 'Rechazado';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                </td>
                                                <td>
                                                    <a href="job_details.php?id=<?php echo $application['job_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Ver Oferta
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No has aplicado a ninguna oferta de trabajo aún. 
                                <a href="jobs.php" class="alert-link">Explora las ofertas disponibles</a>.
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

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


$jobId = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;


$job = getJobById($jobId);


if (!$job || $job['company_id'] != $company['id']) {
    header('Location: manage_jobs.php');
    exit;
}


$applications = getApplicationsByJobId($jobId);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['application_id']) && isset($_POST['status'])) {
    $applicationId = (int)$_POST['application_id'];
    $status = $_POST['status'];
    
    if (in_array($status, ['pending', 'reviewed', 'accepted', 'rejected'])) {
        updateApplicationStatus($applicationId, $status);
    
        $applications = getApplicationsByJobId($jobId);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicaciones - <?php echo htmlspecialchars($job['title']); ?> - Empleate_RD</title>
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
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h2 class="mb-1">Aplicaciones</h2>
                                <p class="text-muted mb-0">Oferta: <?php echo htmlspecialchars($job['title']); ?></p>
                            </div>
                            <a href="manage_jobs.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-1"></i> Volver a Ofertas
                            </a>
                        </div>
                        
                        <?php if ($applications && count($applications) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Candidato</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $application): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($application['photo'])): ?>
                                                            <img src="<?php echo $application['photo']; ?>" alt="Foto de perfil" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                                <i class="bi bi-person text-secondary"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></div>
                                                            <small class="text-muted">ID: <?php echo $application['candidate_id']; ?></small>
                                                        </div>
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
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewApplicationModal<?php echo $application['id']; ?>">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <form method="post" action="">
                                                                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                                    <input type="hidden" name="status" value="pending">
                                                                    <button type="submit" class="dropdown-item">Marcar como Pendiente</button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form method="post" action="">
                                                                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                                    <input type="hidden" name="status" value="reviewed">
                                                                    <button type="submit" class="dropdown-item">Marcar como Revisado</button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form method="post" action="">
                                                                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                                    <input type="hidden" name="status" value="accepted">
                                                                    <button type="submit" class="dropdown-item">Aceptar Candidato</button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form method="post" action="">
                                                                    <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                                    <input type="hidden" name="status" value="rejected">
                                                                    <button type="submit" class="dropdown-item">Rechazar Candidato</button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            <div class="modal fade" id="viewApplicationModal<?php echo $application['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Detalles de la Aplicación</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-4">
                                                                <div class="col-md-4 text-center">
                                                                    <?php if (!empty($application['photo'])): ?>
                                                                        <img src="<?php echo $application['photo']; ?>" alt="Foto de perfil" class="img-fluid rounded-circle mb-2" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                                    <?php else: ?>
                                                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 150px; height: 150px;">
                                                                            <i class="bi bi-person fs-1 text-secondary"></i>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h4><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></h4>
                                                                    
                                                                    <?php if (!empty($application['cv_file'])): ?>
                                                                        <a href="<?php echo $application['cv_file']; ?>" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                                                                            <i class="bi bi-file-earmark-pdf me-1"></i> Ver CV
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    
                                                                    <div class="mt-3">
                                                                        <h6>Estado de la Aplicación:</h6>
                                                                        <div class="btn-group">
                                                                            <form method="post" action="" class="d-inline-block me-1">
                                                                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                                                <input type="hidden" name="status" value="pending">
                                                                                <button type="submit" class="btn btn-sm <?php echo $application['status'] == 'pending' ? 'btn-warning' : 'btn-outline-warning'; ?>">Pendiente</button>
                                                                            </form>
                                                                            <form method="post" action="" class="d-inline-block me-1">
                                                                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                                                <input type="hidden" name="status" value="reviewed">
                                                                                <button type="submit" class="btn btn-sm <?php echo $application['status'] == 'reviewed' ? 'btn-info' : 'btn-outline-info'; ?>">Revisado</button>
                                                                            </form>
                                                                            <form method="post" action="" class="d-inline-block me-1">
                                                                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                                                <input type="hidden" name="status" value="accepted">
                                                                                <button type="submit" class="btn btn-sm <?php echo $application['status'] == 'accepted' ? 'btn-success' : 'btn-outline-success'; ?>">Aceptado</button>
                                                                            </form>
                                                                            <form method="post" action="" class="d-inline-block">
                                                                                <input type="hidden" name="application_id" value="<?php echo $application['id']; ?>">
                                                                                <input type="hidden" name="status" value="rejected">
                                                                                <button type="submit" class="btn btn-sm <?php echo $application['status'] == 'rejected' ? 'btn-danger' : 'btn-outline-danger'; ?>">Rechazado</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <?php if (!empty($application['cover_letter'])): ?>
                                                                <div class="mb-4">
                                                                    <h5>Carta de Presentación</h5>
                                                                    <div class="p-3 bg-light rounded">
                                                                        <?php echo nl2br(htmlspecialchars($application['cover_letter'])); ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <h5>Información Personal</h5>
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                            <span>Nombre Completo:</span>
                                                                            <span class="text-muted"><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></span>
                                                                        </li>
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                            <span>Ciudad:</span>
                                                                            <span class="text-muted"><?php echo htmlspecialchars($application['city'] ?? 'No especificado'); ?></span>
                                                                        </li>
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                            <span>Disponibilidad:</span>
                                                                            <span class="text-muted"><?php echo htmlspecialchars($application['availability'] ?? 'No especificado'); ?></span>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <h5>Resumen Profesional</h5>
                                                                    <div class="p-3 bg-light rounded">
                                                                        <?php echo nl2br(htmlspecialchars($application['summary'] ?? 'No hay resumen disponible.')); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <h5>Habilidades</h5>
                                                                    <div class="p-3 bg-light rounded">
                                                                        <?php echo nl2br(htmlspecialchars($application['skills'] ?? 'No hay habilidades disponibles.')); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <h5>Idiomas</h5>
                                                                    <div class="p-3 bg-light rounded">
                                                                        <?php echo nl2br(htmlspecialchars($application['languages'] ?? 'No hay idiomas disponibles.')); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <h5>Educación</h5>
                                                                <div class="p-3 bg-light rounded">
                                                                    <?php echo nl2br(htmlspecialchars($application['education'] ?? 'No hay información de educación disponible.')); ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <h5>Experiencia Laboral</h5>
                                                                <div class="p-3 bg-light rounded">
                                                                    <?php echo nl2br(htmlspecialchars($application['experience'] ?? 'No hay información de experiencia disponible.')); ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <h5>Logros</h5>
                                                                <div class="p-3 bg-light rounded">
                                                                    <?php echo nl2br(htmlspecialchars($application['achievements'] ?? 'No hay logros disponibles.')); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                No hay aplicaciones para esta oferta de trabajo aún.
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

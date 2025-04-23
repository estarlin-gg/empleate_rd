<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


$filters = [
    'title' => $_GET['title'] ?? '',
    'location' => $_GET['location'] ?? ''
];


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$jobs = getAllJobs($perPage, $offset, $filters);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas de Empleo - Empleate_RD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include 'includes/header.php'; ?>

    <main class="container py-5 flex-grow-1">
        <h1 class="mb-4">Ofertas de Empleo</h1>
        

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="" method="get" class="row g-3">
                    <div class="col-md-5">
                        <label for="title" class="form-label">Puesto o Palabra Clave</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($filters['title']); ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="location" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($filters['location']); ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        

        <div class="row">
            <?php if ($jobs && count($jobs) > 0): ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <?php if (!empty($job['company_logo'])): ?>
                                        <img src="<?php echo $job['company_logo']; ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>" class="me-3" style="width: 50px; height: 50px; object-fit: contain;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="bi bi-building text-secondary fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($job['title']); ?></h5>
                                        <p class="card-subtitle text-muted"><?php echo htmlspecialchars($job['company_name']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="card-text"><?php echo substr(htmlspecialchars($job['description']), 0, 150) . '...'; ?></p>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <?php if (!empty($job['location'])): ?>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($job['location']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($job['job_type'])): ?>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-briefcase me-1"></i> <?php echo htmlspecialchars($job['job_type']); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($job['salary'])): ?>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-cash me-1"></i> <?php echo htmlspecialchars($job['salary']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Publicado: <?php echo date('d/m/Y', strtotime($job['created_at'])); ?></small>
                                    <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-outline-primary btn-sm">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&title=<?php echo urlencode($filters['title']); ?>&location=<?php echo urlencode($filters['location']); ?>">Anterior</a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item active">
                            <span class="page-link"><?php echo $page; ?></span>
                        </li>
                        
                        <?php if (count($jobs) == $perPage): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&title=<?php echo urlencode($filters['title']); ?>&location=<?php echo urlencode($filters['location']); ?>">Siguiente</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No se encontraron ofertas de trabajo que coincidan con tu búsqueda.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

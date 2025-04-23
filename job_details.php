<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


$jobId = isset($_GET['id']) ? (int) $_GET['id'] : 0;


$job = getJobById($jobId);

if (!$job) {
    header('Location: jobs.php');
    exit;
}


$isCandidate = isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'candidate';
$candidateId = null;

if ($isCandidate) {
    $candidate = getCandidateByUserId($_SESSION['user_id']);
    $candidateId = $candidate ? $candidate['id'] : null;
}

$errors = [];
$success = false;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $isCandidate && $candidateId) {
    $coverLetter = $_POST['cover_letter'] ?? '';

    $result = applyForJob([
        'job_id' => $jobId,
        'candidate_id' => $candidateId,
        'cover_letter' => $coverLetter
    ]);

    if ($result['success']) {
        $success = true;
    } else {
        $errors[] = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['title']); ?> - Empleate_RD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'includes/header.php'; ?>

    <main class="container py-5 flex-grow-1">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <?php if (!empty($job['company_logo'])): ?>
                                <img src="<?php echo $job['company_logo']; ?>"
                                    alt="<?php echo htmlspecialchars($job['company_name']); ?>" class="me-3"
                                    style="width: 70px; height: 70px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center me-3"
                                    style="width: 70px; height: 70px;">
                                    <i class="bi bi-building text-secondary fs-3"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h1 class="h3 mb-1"><?php echo htmlspecialchars($job['title']); ?></h1>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($job['company_name']); ?></p>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <?php if (!empty($job['location'])): ?>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt me-2 text-primary"></i>
                                    <span><?php echo htmlspecialchars($job['location']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($job['job_type'])): ?>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-briefcase me-2 text-primary"></i>
                                    <span><?php echo htmlspecialchars($job['job_type']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($job['salary'])): ?>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cash me-2 text-primary"></i>
                                    <span><?php echo htmlspecialchars($job['salary']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar me-2 text-primary"></i>
                                <span>Publicado: <?php echo date('d/m/Y', strtotime($job['created_at'])); ?></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Descripción del Puesto</h5>
                            <div class="mb-4">
                                <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                            </div>

                            <h5>Requisitos</h5>
                            <div>
                                <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
                            </div>
                        </div>

                        <?php if ($isCandidate && $candidateId): ?>
                            <div class="mt-4">
                                <?php if ($success): ?>
                                    <div class="alert alert-success">
                                        Has aplicado correctamente a esta oferta de trabajo.
                                    </div>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#applyModal">
                                        Aplicar a esta oferta
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php elseif (!isset($_SESSION['user_id'])): ?>
                            <div class="alert alert-info mt-4">
                                <p class="mb-0">Para aplicar a esta oferta, debes <a href="login.php"
                                        class="alert-link">iniciar sesión</a> o <a href="register.php?type=candidate"
                                        class="alert-link">registrarte como candidato</a>.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Sobre la Empresa</h5>
                        <p class="card-text"><?php echo htmlspecialchars($job['company_name']); ?></p>
                        <a href="company_details.php?id=<?php echo $job['company_id']; ?>"
                            class="btn btn-outline-primary btn-sm">Ver Perfil de la Empresa</a>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Compartir Oferta</h5>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <?php if ($isCandidate && $candidateId): ?>
        <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="applyModalLabel">Aplicar a:
                            <?php echo htmlspecialchars($job['title']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="">
                        <div class="modal-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="cover_letter" class="form-label">Carta de Presentación (opcional)</label>
                                <textarea class="form-control" id="cover_letter" name="cover_letter" rows="6"
                                    placeholder="Escribe una breve carta de presentación para destacar por qué eres el candidato ideal para este puesto..."></textarea>
                            </div>

                            <div class="alert alert-info">
                                <p class="mb-0">Tu perfil y CV serán compartidos con la empresa cuando apliques.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Enviar Aplicación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
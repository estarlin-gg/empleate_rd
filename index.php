<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleate_RD - Plataforma de Empleos</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main class="container py-5">
        <?php if (!isset($_SESSION['user_id'])): ?>

            <section class="py-5 text-center hero">
                <div class="row py-lg-5">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <h1 class="fw-bold">Encuentra tu próximo empleo o al candidato ideal</h1>
                        <p class="lead text-muted">Conectamos a profesionales talentosos con las mejores empresas.
                            Regístrate hoy y comienza tu camino hacia el éxito profesional.</p>
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="register.php?type=candidate" class="btn btn-primary btn-lg px-4">Soy Candidato</a>
                            <a href="register.php?type=company" class="btn btn-outline-primary btn-lg px-4">Soy Empresa</a>
                        </div>
                    </div>
                </div>
            </section>


            <section class="py-5">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-person-badge fs-1 text-primary mb-3"></i>
                                <h3 class="card-title">Para Candidatos</h3>
                                <p class="card-text">Crea tu perfil profesional, sube tu CV y aplica a cientos de ofertas de
                                    trabajo con un solo clic.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-building fs-1 text-primary mb-3"></i>
                                <h3 class="card-title">Para Empresas</h3>
                                <p class="card-text">Publica ofertas de trabajo y encuentra a los mejores talentos para tu
                                    empresa de forma rápida y eficiente.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center p-4">
                                <i class="bi bi-graph-up-arrow fs-1 text-primary mb-3"></i>
                                <h3 class="card-title">Crecimiento</h3>
                                <p class="card-text">Impulsa tu carrera o tu empresa con nuestra plataforma diseñada para el
                                    éxito profesional.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <section class="mb-5">
                <h2 class="mb-4">Ofertas de Empleo Recientes</h2>
                <div class="row">
                    <?php
                    $jobs = getLatestJobs(6);
                    if ($jobs && count($jobs) > 0):
                        foreach ($jobs as $job):
                            ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <?php echo htmlspecialchars($job['company_name']); ?></h6>
                                        <p class="card-text">
                                            <?php echo substr(htmlspecialchars($job['description']), 0, 100) . '...'; ?></p>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0">
                                        <a href="job_details.php?id=<?php echo $job['id']; ?>"
                                            class="btn btn-sm btn-outline-primary">Ver Detalles</a>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <div class="col-12">
                            <div class="alert alert-info">No hay ofertas de empleo disponibles en este momento.</div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="jobs.php" class="btn btn-primary">Ver Todas las Ofertas</a>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>
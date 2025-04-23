<header class="bg-white shadow-sm">
    <nav class="container navbar navbar-expand-lg navbar-light py-3">
        <a class="navbar-brand fw-bold text-primary" href="index.php">Empleate_RD</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="jobs.php">Ofertas</a>
                </li>
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'company'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="post_job.php">Publicar Oferta</a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            Mi Cuenta
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if($_SESSION['user_role'] == 'candidate'): ?>
                                <li><a class="dropdown-item" href="candidate_profile.php">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="my_applications.php">Mis Aplicaciones</a></li>
                            <?php elseif($_SESSION['user_role'] == 'company'): ?>
                                <li><a class="dropdown-item" href="company_profile.php">Perfil de Empresa</a></li>
                                <li><a class="dropdown-item" href="manage_jobs.php">Gestionar Ofertas</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary me-2">Iniciar Sesión</a>
                    <a href="register.php" class="btn btn-primary">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

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

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $data = [
        'company_id' => $company['id'],
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'requirements' => $_POST['requirements'] ?? '',
        'location' => $_POST['location'] ?? '',
        'job_type' => $_POST['job_type'] ?? '',
        'salary' => $_POST['salary'] ?? '',
    ];
    
    if (empty($data['title'])) {
        $errors[] = 'El título es obligatorio.';
    }
    
    if (empty($data['description'])) {
        $errors[] = 'La descripción es obligatoria.';
    }
    
    if (empty($data['requirements'])) {
        $errors[] = 'Los requisitos son obligatorios.';
    }
    
    if (empty($errors)) {
        $result = createJob($data);
        
        if ($result['success']) {
            $success = true;
            
            $data = [
                'company_id' => $company['id'],
                'title' => '',
                'description' => '',
                'requirements' => '',
                'location' => '',
                'job_type' => '',
                'salary' => '',
            ];
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Oferta de Trabajo - Empleate_RD</title>
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
                                <a class="nav-link active" href="post_job.php">Publicar Oferta</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="manage_jobs.php">Gestionar Ofertas</a>
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
                        <h2 class="mb-4">Publicar Oferta de Trabajo</h2>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                Oferta de trabajo publicada correctamente.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">Título del Puesto *</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo $data['title'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción del Puesto *</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $data['description'] ?? ''; ?></textarea>
                                <div class="form-text">Incluye información sobre las responsabilidades, el equipo, la empresa, etc.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="requirements" class="form-label">Requisitos *</label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="5" required><?php echo $data['requirements'] ?? ''; ?></textarea>
                                <div class="form-text">Incluye habilidades, experiencia, educación, certificaciones, etc.</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Ubicación</label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo $data['location'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="job_type" class="form-label">Tipo de Empleo</label>
                                    <select class="form-select" id="job_type" name="job_type">
                                        <option value="">Seleccionar...</option>
                                        <option value="Tiempo completo" <?php echo (isset($data['job_type']) && $data['job_type'] == 'Tiempo completo') ? 'selected' : ''; ?>>Tiempo completo</option>
                                        <option value="Medio tiempo" <?php echo (isset($data['job_type']) && $data['job_type'] == 'Medio tiempo') ? 'selected' : ''; ?>>Medio tiempo</option>
                                        <option value="Contrato" <?php echo (isset($data['job_type']) && $data['job_type'] == 'Contrato') ? 'selected' : ''; ?>>Contrato</option>
                                        <option value="Freelance" <?php echo (isset($data['job_type']) && $data['job_type'] == 'Freelance') ? 'selected' : ''; ?>>Freelance</option>
                                        <option value="Prácticas" <?php echo (isset($data['job_type']) && $data['job_type'] == 'Prácticas') ? 'selected' : ''; ?>>Prácticas</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="salary" class="form-label">Salario (opcional)</label>
                                <input type="text" class="form-control" id="salary" name="salary" value="<?php echo $data['salary'] ?? ''; ?>" placeholder="Ej: $50,000 - $70,000 anual">
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">Publicar Oferta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is a candidate
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'candidate') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$isNewProfile = isset($_GET['new']) && $_GET['new'] == 1;
$candidate = getCandidateByUserId($userId);
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = [
        'user_id' => $userId,
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'education' => $_POST['education'] ?? '',
        'experience' => $_POST['experience'] ?? '',
        'skills' => $_POST['skills'] ?? '',
        'languages' => $_POST['languages'] ?? '',
        'summary' => $_POST['summary'] ?? '',
        'achievements' => $_POST['achievements'] ?? '',
        'availability' => $_POST['availability'] ?? '',
        'social_links' => $_POST['social_links'] ?? '',
        'references_info' => $_POST['references_info'] ?? '',
    ];


    if (isset($_FILES['photo']) && $_FILES['photo']['error'] != UPLOAD_ERR_NO_FILE) {
        $photoResult = uploadFile(
            $_FILES['photo'],
            'uploads/photos',
            ['image/jpeg', 'image/png', 'image/gif'],
            2097152
        );

        if ($photoResult['success']) {
            $data['photo'] = $photoResult['file_path'];
        } else {
            $errors[] = 'Error al subir la foto: ' . $photoResult['message'];
        }
    }


    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $cvResult = uploadFile(
            $_FILES['cv_file'],
            'uploads/cvs',
            ['application/pdf'],
            5242880
        );

        if ($cvResult['success']) {
            $data['cv_file'] = $cvResult['file_path'];
        } else {
            $errors[] = 'Error al subir el CV: ' . $cvResult['message'];
        }
    }

    if (empty($errors)) {
        if ($candidate) {

            $data['id'] = $candidate['id'];
            $result = updateCandidateProfile($data);
        } else {

            $result = createCandidateProfile($data);
        }

        if ($result['success']) {
            $success = true;
            $candidate = getCandidateByUserId($userId);
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
    <title>Perfil de Candidato - Empleate_RD</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
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
                                <a class="nav-link active" href="candidate_profile.php">Mi Perfil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="my_applications.php">Mis Aplicaciones</a>
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
                        <h2 class="mb-4"><?php echo $isNewProfile ? 'Completa tu Perfil' : 'Editar Perfil'; ?></h2>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                Perfil guardado correctamente.
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

                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">Nombre(s) *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="<?php echo $candidate['first_name'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Apellido(s) *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="<?php echo $candidate['last_name'] ?? ''; ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?php echo $candidate['phone'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">Ciudad / Provincia</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="<?php echo $candidate['city'] ?? ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="<?php echo $candidate['address'] ?? ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="summary" class="form-label">Objetivo Profesional / Resumen</label>
                                <textarea class="form-control" id="summary" name="summary"
                                    rows="3"><?php echo $candidate['summary'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="education" class="form-label">Formación Académica</label>
                                <textarea class="form-control" id="education" name="education" rows="3"
                                    placeholder="Institución, título, fechas"><?php echo $candidate['education'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="experience" class="form-label">Experiencia Laboral</label>
                                <textarea class="form-control" id="experience" name="experience" rows="3"
                                    placeholder="Empresa, puesto, fechas, responsabilidades"><?php echo $candidate['experience'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="skills" class="form-label">Habilidades Clave</label>
                                <textarea class="form-control" id="skills" name="skills"
                                    rows="2"><?php echo $candidate['skills'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="languages" class="form-label">Idiomas</label>
                                <textarea class="form-control" id="languages" name="languages"
                                    rows="2"><?php echo $candidate['languages'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="achievements" class="form-label">Logros o Proyectos Destacados</label>
                                <textarea class="form-control" id="achievements" name="achievements"
                                    rows="3"><?php echo $candidate['achievements'] ?? ''; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="availability" class="form-label">Disponibilidad</label>
                                    <select class="form-select" id="availability" name="availability">
                                        <option value="">Seleccionar...</option>
                                        <option value="Inmediata" <?php echo (isset($candidate['availability']) && $candidate['availability'] == 'Inmediata') ? 'selected' : ''; ?>>Inmediata
                                        </option>
                                        <option value="2 semanas" <?php echo (isset($candidate['availability']) && $candidate['availability'] == '2 semanas') ? 'selected' : ''; ?>>2 semanas
                                        </option>
                                        <option value="1 mes" <?php echo (isset($candidate['availability']) && $candidate['availability'] == '1 mes') ? 'selected' : ''; ?>>1 mes</option>
                                        <option value="Más de 1 mes" <?php echo (isset($candidate['availability']) && $candidate['availability'] == 'Más de 1 mes') ? 'selected' : ''; ?>>Más de 1
                                            mes</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="social_links" class="form-label">Redes Profesionales (LinkedIn,
                                    etc.)</label>
                                <textarea class="form-control" id="social_links" name="social_links"
                                    rows="2"><?php echo $candidate['social_links'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="references_info" class="form-label">Referencias</label>
                                <textarea class="form-control" id="references_info" name="references_info" rows="2"
                                    placeholder="Nombre, empresa, contacto"><?php echo $candidate['references_info'] ?? ''; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="photo" class="form-label">Foto (opcional)</label>
                                    <?php if (!empty($candidate['photo'])): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo $candidate['photo']; ?>" alt="Foto de perfil"
                                                class="img-thumbnail" style="max-width: 100px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cv_file" class="form-label">CV en PDF (opcional)</label>
                                    <?php if (!empty($candidate['cv_file'])): ?>
                                        <div class="mb-2">
                                            <a href="<?php echo $candidate['cv_file']; ?>" target="_blank"
                                                class="btn btn-sm btn-outline-primary">Ver CV actual</a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="cv_file" name="cv_file"
                                        accept="application/pdf">
                                </div>
                                accept="application/pdf">
                            </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Guardar Perfil</button>
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
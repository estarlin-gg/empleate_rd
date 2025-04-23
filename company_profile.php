<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'company') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$isNewProfile = isset($_GET['new']) && $_GET['new'] == 1;
$company = getCompanyByUserId($userId);
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = [
        'user_id' => $userId,
        'name' => $_POST['name'] ?? '',
        'address' => $_POST['address'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'website' => $_POST['website'] ?? '',
        'description' => $_POST['description'] ?? '',
    ];


    if (isset($_FILES['logo']) && $_FILES['logo']['error'] != UPLOAD_ERR_NO_FILE) {
        $logoResult = uploadFile(
            $_FILES['logo'],
            'uploads/logos',
            ['image/jpeg', 'image/png', 'image/gif'],
            2097152
        );

        if ($logoResult['success']) {
            $data['logo'] = $logoResult['file_path'];
        } else {
            $errors[] = 'Error al subir el logo: ' . $logoResult['message'];
        }
    }

    if (empty($errors)) {
        if ($company) {

            $data['id'] = $company['id'];
            $result = updateCompanyProfile($data);
        } else {

            $result = createCompanyProfile($data);
        }

        if ($result['success']) {
            $success = true;
            $company = getCompanyByUserId($userId);
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
    <title>Perfil de Empresa - Empleate_RD</title>
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
                                <a class="nav-link active" href="company_profile.php">Perfil de Empresa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="post_job.php">Publicar Oferta</a>
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
                        <h2 class="mb-4">
                            <?php echo $isNewProfile ? 'Completa el Perfil de tu Empresa' : 'Editar Perfil de Empresa'; ?>
                        </h2>

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
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre de la Empresa *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo $company['name'] ?? ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="<?php echo $company['address'] ?? ''; ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?php echo $company['phone'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="website" class="form-label">Sitio Web</label>
                                    <input type="url" class="form-control" id="website" name="website"
                                        value="<?php echo $company['website'] ?? ''; ?>" placeholder="https://">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción de la Empresa</label>
                                <textarea class="form-control" id="description" name="description"
                                    rows="5"><?php echo $company['description'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo de la Empresa (opcional)</label>
                                <?php if (!empty($company['logo'])): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo $company['logo']; ?>" alt="Logo de la empresa"
                                            class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
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
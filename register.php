<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'candidate';
if ($type != 'candidate' && $type != 'company') {
    $type = 'candidate';
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (!$email) {
        $errors[] = 'Por favor, introduce un correo electrónico válido.';
    }
    
    if (strlen($password) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Las contraseñas no coinciden.';
    }
    
    if (empty($errors)) {
        $result = registerUser($email, $password, $type);
        
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_role'] = $type;
            

            if ($type == 'candidate') {
                header('Location: candidate_profile.php?new=1');
            } else {
                header('Location: company_profile.php?new=1');
            }
            exit;
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
    <title>Registro - Empleate_RD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include 'includes/header.php'; ?>

    <main class="container py-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Crear una cuenta</h2>
                        
                        <ul class="nav nav-pills nav-justified mb-4">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $type == 'candidate' ? 'active' : ''; ?>" href="?type=candidate">Candidato</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $type == 'company' ? 'active' : ''; ?>" href="?type=company">Empresa</a>
                            </li>
                        </ul>
                        
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
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">La contraseña debe tener al menos 6 caracteres.</div>
                            </div>
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Registrarse</button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar Sesión</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

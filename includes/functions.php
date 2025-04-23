<?php

function registerUser($email, $password, $role)
{
    try {
        $conn = getConnection();


        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Este correo electrónico ya está registrado.'];
        }


        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, :role)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        return ['success' => true, 'user_id' => $conn->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
    }
}

function loginUser($email, $password)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            return ['success' => false, 'message' => 'Correo electrónico o contraseña incorrectos.'];
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            return [
                'success' => true,
                'user_id' => $user['id'],
                'role' => $user['role']
            ];
        } else {
            return ['success' => false, 'message' => 'Correo electrónico o contraseña incorrectos.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al iniciar sesión: ' . $e->getMessage()];
    }
}

function getUserById($userId)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("SELECT id, email, role FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}


function createCandidateProfile($data)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("INSERT INTO candidates (
            user_id, first_name, last_name, phone, address, city, 
            education, experience, skills, languages, summary, 
            achievements, availability, social_links, references_info, 
            photo, cv_file
        ) VALUES (
            :user_id, :first_name, :last_name, :phone, :address, :city, 
            :education, :experience, :skills, :languages, :summary, 
            :achievements, :availability, :social_links, :references_info, 
            :photo, :cv_file
        )");

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':education', $data['education']);
        $stmt->bindParam(':experience', $data['experience']);
        $stmt->bindParam(':skills', $data['skills']);
        $stmt->bindParam(':languages', $data['languages']);
        $stmt->bindParam(':summary', $data['summary']);
        $stmt->bindParam(':achievements', $data['achievements']);
        $stmt->bindParam(':availability', $data['availability']);
        $stmt->bindParam(':social_links', $data['social_links']);
        $stmt->bindParam(':references_info', $data['references_info']);
        $stmt->bindParam(':photo', $data['photo']);
        $stmt->bindParam(':cv_file', $data['cv_file']);

        $stmt->execute();

        return ['success' => true, 'candidate_id' => $conn->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al crear perfil: ' . $e->getMessage()];
    }
}

function updateCandidateProfile($data)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("UPDATE candidates SET 
            first_name = :first_name, 
            last_name = :last_name, 
            phone = :phone, 
            address = :address, 
            city = :city, 
            education = :education, 
            experience = :experience, 
            skills = :skills, 
            languages = :languages, 
            summary = :summary, 
            achievements = :achievements, 
            availability = :availability, 
            social_links = :social_links, 
            references_info = :references_info
            " . (!empty($data['photo']) ? ", photo = :photo" : "") . "
            " . (!empty($data['cv_file']) ? ", cv_file = :cv_file" : "") . "
            WHERE id = :id AND user_id = :user_id");

        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':education', $data['education']);
        $stmt->bindParam(':experience', $data['experience']);
        $stmt->bindParam(':skills', $data['skills']);
        $stmt->bindParam(':languages', $data['languages']);
        $stmt->bindParam(':summary', $data['summary']);
        $stmt->bindParam(':achievements', $data['achievements']);
        $stmt->bindParam(':availability', $data['availability']);
        $stmt->bindParam(':social_links', $data['social_links']);
        $stmt->bindParam(':references_info', $data['references_info']);

        if (!empty($data['photo'])) {
            $stmt->bindParam(':photo', $data['photo']);
        }

        if (!empty($data['cv_file'])) {
            $stmt->bindParam(':cv_file', $data['cv_file']);
        }

        $stmt->execute();

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar perfil: ' . $e->getMessage()];
    }
}

function getCandidateByUserId($userId)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("SELECT * FROM candidates WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function getCandidateById($id)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("
            SELECT c.*, u.email 
            FROM candidates c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function createCompanyProfile($data)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("INSERT INTO companies (
            user_id, name, address, phone, website, description, logo
        ) VALUES (
            :user_id, :name, :address, :phone, :website, :description, :logo
        )");

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':website', $data['website']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':logo', $data['logo']);

        $stmt->execute();

        return ['success' => true, 'company_id' => $conn->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al crear perfil de empresa: ' . $e->getMessage()];
    }
}

function updateCompanyProfile($data)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("UPDATE companies SET 
            name = :name, 
            address = :address, 
            phone = :phone, 
            website = :website, 
            description = :description
            " . (!empty($data['logo']) ? ", logo = :logo" : "") . "
            WHERE id = :id AND user_id = :user_id");

        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':website', $data['website']);
        $stmt->bindParam(':description', $data['description']);

        if (!empty($data['logo'])) {
            $stmt->bindParam(':logo', $data['logo']);
        }

        $stmt->execute();

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar perfil de empresa: ' . $e->getMessage()];
    }
}

function getCompanyByUserId($userId)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("SELECT * FROM companies WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function getCompanyById($id)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("
            SELECT c.*, u.email 
            FROM companies c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}


function createJob($data)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("INSERT INTO jobs (
            company_id, title, description, requirements, location, job_type, salary
        ) VALUES (
            :company_id, :title, :description, :requirements, :location, :job_type, :salary
        )");

        $stmt->bindParam(':company_id', $data['company_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':requirements', $data['requirements']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':job_type', $data['job_type']);
        $stmt->bindParam(':salary', $data['salary']);

        $stmt->execute();

        return ['success' => true, 'job_id' => $conn->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al crear oferta de trabajo: ' . $e->getMessage()];
    }
}

function updateJob($data)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("UPDATE jobs SET 
            title = :title, 
            description = :description, 
            requirements = :requirements, 
            location = :location, 
            job_type = :job_type, 
            salary = :salary,
            status = :status
            WHERE id = :id AND company_id = :company_id");

        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':company_id', $data['company_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':requirements', $data['requirements']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':job_type', $data['job_type']);
        $stmt->bindParam(':salary', $data['salary']);
        $stmt->bindParam(':status', $data['status']);

        $stmt->execute();

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar oferta de trabajo: ' . $e->getMessage()];
    }
}

function getJobById($id)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("
            SELECT j.*, c.name as company_name, c.logo as company_logo
            FROM jobs j
            JOIN companies c ON j.company_id = c.id
            WHERE j.id = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function getJobsByCompanyId($companyId)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("
            SELECT j.*, 
                (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as applications_count
            FROM jobs j
            WHERE j.company_id = :company_id
            ORDER BY j.created_at DESC
        ");
        $stmt->bindParam(':company_id', $companyId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function getAllJobs($limit = null, $offset = 0, $filters = [])
{
    try {
        $conn = getConnection();

        $sql = "
            SELECT j.*, c.name as company_name, c.logo as company_logo
            FROM jobs j
            JOIN companies c ON j.company_id = c.id
            WHERE j.status = 'active'
        ";


        if (!empty($filters['title'])) {
            $sql .= " AND j.title LIKE :title";
        }

        if (!empty($filters['location'])) {
            $sql .= " AND j.location LIKE :location";
        }

        $sql .= " ORDER BY j.created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $conn->prepare($sql);


        if (!empty($filters['title'])) {
            $titleSearch = '%' . $filters['title'] . '%';
            $stmt->bindParam(':title', $titleSearch);
        }

        if (!empty($filters['location'])) {
            $locationSearch = '%' . $filters['location'] . '%';
            $stmt->bindParam(':location', $locationSearch);
        }

        if ($limit !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function getLatestJobs($limit = 6)
{
    return getAllJobs($limit);
}


function applyForJob($data)
{
    try {
        $conn = getConnection();

        // Check if already applied
        $stmt = $conn->prepare("SELECT id FROM applications WHERE job_id = :job_id AND candidate_id = :candidate_id");
        $stmt->bindParam(':job_id', $data['job_id']);
        $stmt->bindParam(':candidate_id', $data['candidate_id']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Ya has aplicado a esta oferta de trabajo.'];
        }

        $stmt = $conn->prepare("INSERT INTO applications (
            job_id, candidate_id, cover_letter
        ) VALUES (
            :job_id, :candidate_id, :cover_letter
        )");

        $stmt->bindParam(':job_id', $data['job_id']);
        $stmt->bindParam(':candidate_id', $data['candidate_id']);
        $stmt->bindParam(':cover_letter', $data['cover_letter']);

        $stmt->execute();

        return ['success' => true, 'application_id' => $conn->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al aplicar: ' . $e->getMessage()];
    }
}

function updateApplicationStatus($id, $status)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("UPDATE applications SET status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error al actualizar estado: ' . $e->getMessage()];
    }
}

function getApplicationsByJobId($jobId)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("
            SELECT a.*, c.first_name, c.last_name, c.photo, c.cv_file
            FROM applications a
            JOIN candidates c ON a.candidate_id = c.id
            WHERE a.job_id = :job_id
            ORDER BY a.created_at DESC
        ");
        $stmt->bindParam(':job_id', $jobId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function getApplicationsByCandidateId($candidateId)
{
    try {
        $conn = getConnection();

        $stmt = $conn->prepare("
            SELECT a.*, j.title as job_title, c.name as company_name, c.logo as company_logo
            FROM applications a
            JOIN jobs j ON a.job_  c.logo as company_logo
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            JOIN companies c ON j.company_id = c.id
            WHERE a.candidate_id = :candidate_id
            ORDER BY a.created_at DESC
        ");
        $stmt->bindParam(':candidate_id', $candidateId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}


function uploadFile($file, $targetDir, $allowedTypes = [], $maxSize = 5242880)
{

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error al subir el archivo.'];
    }


    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'El archivo es demasiado grande.'];
    }


    if (!empty($allowedTypes)) {
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Tipo de archivo no permitido.'];
        }
    }


    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }


    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExtension;
    $targetPath = $targetDir . '/' . $fileName;


    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'file_path' => $targetPath];
    } else {
        return ['success' => false, 'message' => 'Error al guardar el archivo.'];
    }
}

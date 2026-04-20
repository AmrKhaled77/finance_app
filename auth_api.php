<?php
session_start();
require "connection.php";
global $conn;
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'signup') {
        $username = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit;
        }

        // 1. Check if email exists
        $statementCheck = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $statementCheck->bind_param("s", $email);
        $statementCheck->execute();
        $result = $statementCheck->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email already in use.']);
            $statementCheck->close();
            exit;
        }
        $statementCheck->close();

        // --- NEW: SERVER-SIDE FILE HANDLING ---
        $profile_pic_path = null; // Default to null if no picture is uploaded

        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_pic'];

            // Server-Side Size Validation (2MB)
            if ($file['size'] > 2097152) {
                echo json_encode(['status' => 'error', 'message' => 'Image exceeds 2MB limit.']);
                exit;
            }

            // Server-Side Type Validation (Strict inspection of file bytes, not just the extension)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($mime_type, $allowed_types)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG/PNG allowed.']);
                exit;
            }

            // Create safe directory and filename
            $upload_dir = 'uploads/profiles/';

            // If the folder doesn't exist, create it automatically
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Generate a unique random string for the filename so users don't overwrite each other
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('user_', true) . '.' . $extension;
            $destination = $upload_dir . $new_filename;

            // Move the file from PHP's temp cache to our permanent folder
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $profile_pic_path = $destination; // Success! Save this string to go into the DB
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save image to server.']);
                exit;
            }
        }
        // --------------------------------------

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 2. Updated INSERT statement to include the new column
        $statement = $conn->prepare("INSERT INTO users (username, email, password, profile_image_path) VALUES (?, ?, ?, ?)");
        // We now bind 4 parameters (ssss)
        $statement->bind_param("ssss", $username, $email, $hashed_password, $profile_pic_path);

        if ($statement->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'User registered successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database Error: Could not create account.']);
        }

        $statement->close();
        exit;
    }
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit;
        }

        $stmnt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmnt->bind_param("s", $email);
        $stmnt->execute();
        $result = $stmnt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['profile_image'] = $user['profile_image_path'];
                echo json_encode(['status' => 'success', 'message' => 'Login successful']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
        }
        $stmnt->close();
        exit;
    }
}
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
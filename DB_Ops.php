<?php
include "connection.php";
header("Content-Type: application/json");

// ================= VALIDATION FUNCTION =================
function validateTransaction($data) {

    $errors = [];

    // TITLE
    if (empty(trim($data['title'] ?? ''))) {
        $errors['title'] = "Title is required";
    } elseif (strlen($data['title']) < 3) {
        $errors['title'] = "Minimum 3 characters";
    }

    // AMOUNT
    if (!isset($data['amount']) || $data['amount'] === '') {
        $errors['amount'] = "Amount is required";
    } elseif (!is_numeric($data['amount'])) {
        $errors['amount'] = "Must be a number";
    } elseif ($data['amount'] <= 0) {
        $errors['amount'] = "Must be > 0";
    }

    // TYPE
    if (empty($data['type'])) {
        $errors['type'] = "Type required";
    } elseif (!in_array($data['type'], ['income', 'expense'])) {
        $errors['type'] = "Invalid type";
    }

    // CATEGORY
    if (empty(trim($data['category'] ?? ''))) {
        $errors['category'] = "Category required";
    }

    // DATE
    if (empty($data['date'])) {
        $errors['date'] = "Date required";
    } else {
        $date = strtotime($data['date']);
        if (!$date) {
            $errors['date'] = "Invalid date";
        } elseif ($date > strtotime(date("Y-m-d"))) {
            $errors['date'] = "Future date not allowed";
        }
    }

    return $errors;
}


// ================= READ =================
if (isset($_GET['action']) && $_GET['action'] == "get") {

    $result = $conn->query("SELECT * FROM transactions ORDER BY date DESC");

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}


// ================= ADD =================
if (isset($_POST['action']) && $_POST['action'] == "add") {

    $errors = validateTransaction($_POST);

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "errors" => $errors]);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO transactions (title, amount, type, category, date)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sdsss",
        $_POST['title'],
        $_POST['amount'],
        $_POST['type'],
        $_POST['category'],
        $_POST['date']
    );

    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Transaction added"]);
    exit;
}


// ================= UPDATE =================
if (isset($_POST['action']) && $_POST['action'] == "update") {

    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        exit;
    }

    $errors = validateTransaction($_POST);

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "errors" => $errors]);
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE transactions
        SET title=?, amount=?, type=?, category=?, date=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sdsssi",
        $_POST['title'],
        $_POST['amount'],
        $_POST['type'],
        $_POST['category'],
        $_POST['date'],
        $_POST['id']
    );

    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Updated"]);
    exit;
}


// ================= DELETE =================
if (isset($_POST['action']) && $_POST['action'] == "delete") {

    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM transactions WHERE id=?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Deleted"]);
    exit;
}
?>
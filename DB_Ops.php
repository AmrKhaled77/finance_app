<?php
session_start();
include "connection.php";
global $conn;
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

/* ---------------- READ ---------------- */
if (isset($_GET['action']) && $_GET['action'] == "get") {

    $statement = $conn->prepare("SELECT * FROM transactions WHERE userId = ? ORDER BY date DESC");
    $statement->bind_param("i", $_SESSION['user_id']);
    $statement->execute();
    $result = $statement->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    $statement->close();
    exit;
}

/* ---------------- ADD ---------------- */
if (isset($_POST['action']) && $_POST['action'] == "add") {

    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        INSERT INTO transactions (title, amount, type, category, date, userId)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sdsssi", $title, $amount, $type, $category, $date, $user_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Transaction added"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add transaction"]);
    }
    exit;
}

/* ---------------- UPDATE ---------------- */
if (isset($_POST['action']) && $_POST['action'] == "update") {

    $id = $_POST['id'];
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("
        UPDATE transactions 
        SET title=?, amount=?, type=?, category=?, date=? 
        WHERE id=? AND userId = ?
    ");

    $stmt->bind_param("sdsssii", $title, $amount, $type, $category, $date, $id, $_SESSION['user_id']);
    if ($stmt -> execute()) {
        echo json_encode(["status" => "success", "message" => "Transaction updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update transaction"]);
    }
    exit;
}

/* ---------------- DELETE ---------------- */
if (isset($_POST['action']) && $_POST['action'] == "delete") {

    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM transactions WHERE id=? AND userId = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete transaction"]);
    }

    exit;
}

// At the bottom of API_Ops.php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'getAllCurrencies') {
        $data = getAllCurrencies();
    } elseif ($action === 'getRates') {
        $base = $_GET['base'] ?? 'usd';
        $data = getRates($base);
    } else {
        $data = ['error' => 'Unknown action'];
    }

    header('Content-Type: application/json');
    echo json_encode($data);
}
?>
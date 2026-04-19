<?php
include "connection.php";
header("Content-Type: application/json");

/* ---------------- READ ---------------- */
if (isset($_GET['action']) && $_GET['action'] == "get") {

    $result = $conn->query("SELECT * FROM transactions ORDER BY date DESC");

    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}

/* ---------------- ADD ---------------- */
if (isset($_POST['action']) && $_POST['action'] == "add") {

    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("
        INSERT INTO transactions (title, amount, type, category, date)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sdsss", $title, $amount, $type, $category, $date);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Transaction added"]);
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
        WHERE id=?
    ");

    $stmt->bind_param("sdsssi", $title, $amount, $type, $category, $date, $id);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Updated"]);
    exit;
}

/* ---------------- DELETE ---------------- */
if (isset($_POST['action']) && $_POST['action'] == "delete") {

    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM transactions WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Deleted"]);
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
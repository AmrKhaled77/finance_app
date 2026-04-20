<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);

$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
$avatar_url = '';

if (isset($_SESSION['user_id'])) {
    $avatar_url = !empty($_SESSION['profile_image'])
            ? $_SESSION['profile_image']
            : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=d4f88a&color=000';
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FinTrack - Simplify Your Finances</title>
    <link rel="stylesheet" href="css/output.css">
    <link rel="icon" href="css/FinTrackIcon.jpg" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Inter, sans-serif;
        }
    </style>
</head>
<body class="bg-[#fcfdfa] min-h-screen flex flex-col text-gray-800">
<nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
    <a href="index.php" class="text-2xl font-bold text-gray-900 tracking-tight">Fin<span
                class="text-[#d4f88a]">Track</span></a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-500" id="spa-nav">
            <a href="#" data-view="dashboard-view" class="nav-btn text-black font-semibold border-b-2 border-[#d4f88a] pb-1">Dashboard</a>
            <a href="#" data-view="rates-view" class="nav-btn hover:text-black transition">Exchange Rates</a>
        </div>

        <div class="flex items-center space-x-4">
            <div class="text-sm font-medium text-gray-700 hidden sm:block">
                Hello, <?php echo htmlspecialchars($userName); ?>
            </div>

            <img src="<?php echo htmlspecialchars($avatar_url); ?>"
                 alt="Profile"
                 style="width: 36px; height: 36px; min-width: 36px; object-fit: cover; border-radius: 50%; border: 2px solid #c4ec77;"
                 class="shadow-sm">

            <a href="logout.php" class="text-xs text-red-500 hover:underline transition ml-2">Logout</a>
        </div>
    <?php endif; ?>
</nav>

<main class="grow flex flex-col items-center justify-center p-4 lg:p-8 max-w-7xl mx-auto w-full">
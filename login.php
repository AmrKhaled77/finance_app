<?php
session_start();
if (isset($_SESSION['user_id'])) {
    include "dashboard.php";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FinTrack - Login</title>
    <link rel="stylesheet" href="css/output.css">
    <link rel="icon" href="css/FinTrackIcon.jpg" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Inter, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md" id="auth-container">

    <form id="loginForm" class="space-y-4">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
            <p class="text-sm text-gray-500">Enter your details to access your account.</p>
        </div>

        <input type="email" name="email" placeholder="Email Address" required
               class="w-full text-sm px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] focus:ring-1 focus:ring-[#d4f88a] bg-gray-50">
        <input type="password" name="password" placeholder="Password" required
               class="w-full text-sm px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] focus:ring-1 focus:ring-[#d4f88a] bg-gray-50">

        <button type="submit"
                class="w-full bg-[#d4f88a] text-black text-sm font-bold py-3 rounded-lg shadow-sm hover:bg-[#c4ec77] transition mt-2">
            Log In
        </button>
        <p class="text-sm text-center mt-4">Need an account? <a href="#" id="showSignup"
                                                                class="text-black font-bold hover:underline">Sign Up</a>
        </p>
    </form>

    <form id="signupForm" class="space-y-4 hidden">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create an Account</h2>
            <p class="text-sm text-gray-500">Start tracking your finances today.</p>
        </div>

        <input type="text" name="name" placeholder="Full Name" required
               class="w-full text-sm px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] focus:ring-1 focus:ring-[#d4f88a] bg-gray-50">

        <input type="email" name="email" placeholder="Email Address" required
               class="w-full text-sm px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] focus:ring-1 focus:ring-[#d4f88a] bg-gray-50">

        <input type="password" name="password" placeholder="Password" required
               class="w-full text-sm px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] focus:ring-1 focus:ring-[#d4f88a] bg-gray-50">

        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1 ml-1">Profile Picture (Optional)</label>
            <input type="file" name="profile_pic" id="profilePicInput" accept="image/png, image/jpeg, image/jpg"
                   class="w-full text-sm px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] bg-gray-50
                          file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#d4f88a] file:text-black hover:file:bg-[#c4ec77] transition">
        </div>

        <button type="submit"
                class="w-full bg-[#d4f88a] text-black text-sm font-bold py-3 rounded-lg shadow-sm hover:bg-[#c4ec77] transition mt-2">
            Create Account
        </button>
        <p class="text-sm text-center mt-4">Already have an account? <a href="#" id="showLogin"
                                                                        class="text-black font-bold hover:underline">Log
                In</a></p>
    </form>
    <div id="authMessage" class="hidden mt-4 p-3 rounded-lg text-sm text-center font-medium"></div>
</div>

<script src="js/auth.js"></script>
</body>
</html>


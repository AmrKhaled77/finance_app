<?php include "header.php"; ?>

<div class="flex flex-col items-center justify-center w-full my-auto">

    <div class="text-center mb-12 max-w-2xl">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-4">
            Smart Tracking.
            <br>
            Global Insights
        </h1>
        <p class="text-gray-500 mb-8">Manage your transactions and stay updated with real-time currency conversions, all
            in one place.</p>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php"
               class="bg-[#d4f88a] text-black text-base font-semibold py-3 px-8 rounded-full shadow-sm hover:bg-[#c4ec77] transition duration-300 inline-block">
                Get Started
            </a>
        <?php else: ?>
            <a href="dashboard.php"
               class="bg-[#d4f88a] text-black text-base font-semibold py-3 px-8 rounded-full shadow-sm hover:bg-[#c4ec77] transition duration-300 inline-block">
                Go to Dashboard
            </a>
        <?php endif; ?>
    </div>

    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Live Exchange Rates</h2>
            <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="currency-container">
            <div class="p-4 border border-gray-200 rounded-xl bg-gray-50 animate-pulse h-24"></div>
            <div class="p-4 border border-gray-200 rounded-xl bg-gray-50 animate-pulse h-24"></div>
            <div class="p-4 border border-gray-200 rounded-xl bg-gray-50 animate-pulse h-24"></div>
        </div>

        <p class="text-xs text-gray-400 mt-4 text-center" id="last-updated">Fetching latest rates...</p>
    </div>
</div>

<script src="js/exchange-rates.js"></script>

<?php include "footer.php"; ?>

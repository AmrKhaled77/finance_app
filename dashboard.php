<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
$avatar_url = !empty($_SESSION['profile_image'])
        ? $_SESSION['profile_image']
        : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=d4f88a&color=000';

include 'header.php';
?>

<div id="dashboard-view" class="app-view w-full space-y-8">
    <div id="errorBox" style="display:none;" class="bg-red-50 text-red-600 p-4 border border-red-200 rounded-lg text-sm font-medium shadow-sm"></div>

    <div class="flex items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Welcome back, <?php echo htmlspecialchars($userName); ?>!</h2>
            <p class="text-sm text-gray-500">Here is your financial overview.</p>
        </div>

        <div class="flex items-center space-x-3 bg-gray-50 py-2 px-4 rounded-full border border-gray-200">
            <span class="font-bold text-sm text-gray-700"><?php echo htmlspecialchars($userName); ?></span>
            <img src="<?php echo htmlspecialchars($avatar_url); ?>"
                 alt="Profile Picture"
                 style="width: 48px; height: 48px; min-width: 48px; object-fit: cover; border-radius: 50%; border: 2px solid #d4f88a;"
                 class="shadow-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#d4f88a] rounded-2xl p-6 shadow-sm border border-[#c4ec77]">
            <div class="text-sm text-gray-700 font-medium mb-1">Total Balance</div>
            <div class="text-3xl font-bold text-black"><span id="balanceTotal">0</span> EGP</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-center">
            <div class="text-sm text-gray-500 font-medium mb-1">Income</div>
            <div class="text-2xl font-bold text-green-600">+<span id="incomeTotal">0</span> EGP</div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-center">
            <div class="text-sm text-gray-500 font-medium mb-1">Expense</div>
            <div class="text-2xl font-bold text-red-600">-<span id="expenseTotal">0</span> EGP</div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-5">Add Transaction</h3>
                <div class="space-y-4">
                    <div>
                        <input type="text" id="title" placeholder="Title" class="w-full text-sm px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] bg-gray-50 transition">
                        <small id="titleError" style="display:none;" class="text-red-500 text-xs mt-1 font-medium"></small>
                    </div>
                    <div>
                        <input type="number" id="amount" placeholder="Amount" class="w-full text-sm px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] bg-gray-50 transition">
                        <small id="amountError" style="display:none;" class="text-red-500 text-xs mt-1 font-medium"></small>
                    </div>
                    <div>
                        <select id="type" class="w-full text-sm px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] bg-gray-50 text-gray-700 transition">
                            <option value="">Select Type</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                        <small id="typeError" style="display:none;" class="text-red-500 text-xs mt-1 font-medium"></small>
                    </div>
                    <div>
                        <input type="text" id="category" placeholder="Category" class="w-full text-sm px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] bg-gray-50 transition">
                        <small id="categoryError" style="display:none;" class="text-red-500 text-xs mt-1 font-medium"></small>
                    </div>
                    <div>
                        <input type="date" id="date" class="w-full text-sm px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] bg-gray-50 text-gray-700 transition">
                        <small id="dateError" style="display:none;" class="text-red-500 text-xs mt-1 font-medium"></small>
                    </div>
                    <button id="submitBtn" onclick="addTransaction()" class="w-full bg-[#1e1e1e] text-white text-sm font-bold py-3 rounded-lg shadow-sm hover:bg-black transition mt-2">
                        + Add Transaction
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Income vs Expense</h3>
                <div class="chart-container w-full relative" style="min-height: 200px;">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Transactions</h3>
                    <div class="flex items-center gap-2">
                        <label for="globalCurrency" class="text-sm font-medium text-gray-500">View in:</label>
                        <select id="globalCurrency" class="text-sm px-3 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:border-[#d4f88a] focus:ring-1 focus:ring-[#d4f88a] bg-gray-50 transition font-bold text-gray-700">
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="GBP">GBP (£)</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold tracking-wide border-b border-gray-200">
                            <th class="px-4 py-3 rounded-tl-lg">Title</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Actions</th>
                            <th id="convertHeader" class="px-4 py-3 rounded-tr-lg">Converted to USD</th>
                        </tr>
                        </thead>
                        <tbody id="tableData" class="divide-y divide-gray-100 text-sm text-gray-700">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="rates-view" class="app-view hidden w-full my-auto flex flex-col items-center justify-center">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">Live Exchange Rates</h2>
            <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Base: EGP
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

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/script.js"></script>
<script src="js/exchange-rates.js"></script>
<script src="js/spa.js"></script>
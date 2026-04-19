<!DOCTYPE html>
<html>
<head>
    <title>Finance App</title>

    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<header>
    <h1>💰 Finance SPA App</h1>
</header>

<!-- DASHBOARD -->
<div class="stats">
    <div class="stat-box income">Income <span id="incomeTotal">0</span></div>
    <div class="stat-box expense">Expense <span id="expenseTotal">0</span></div>
    <div class="stat-box balance">Balance <span id="balanceTotal">0</span></div>
</div>


<div class="card chart-card">
    <h3>📊 Income vs Expense</h3>

    <div class="chart-container">
        <canvas id="financeChart"></canvas>
    </div>
</div>
<div class="container">

    <!-- FORM -->
   <div class="card form-card">
    <h3>Add / Edit Transaction</h3>

    <input type="text" id="title" placeholder="Title">
    <small  class="error" style="color:#ff3b30; font-size:12px; margin-top:4px; display:block; font-weight:500;" id="titleError"></small>

    <input type="number" id="amount" placeholder="Amount">
    <small  class="error" style="color:#ff3b30; font-size:12px; margin-top:4px; display:block; font-weight:500;" id="amountError"></small>

    <select id="type">
        <option value="">Select Type</option>
        <option value="income">Income</option>
        <option value="expense">Expense</option>
    </select>
    <small  class="error" style="color:#ff3b30; font-size:12px; margin-top:4px; display:block; font-weight:500;" id="typeError"></small>

    <input type="text" id="category" placeholder="Category">
    <small  class="error" style="color:#ff3b30; font-size:12px; margin-top:4px; display:block; font-weight:500;" id="categoryError"></small>

    <input type="date" id="date">
    <small  class="error" style="color:#ff3b30; font-size:12px; margin-top:4px; display:block; font-weight:500;" id="dateError"></small>

    <button id="submitBtn" onclick="addTransaction()">+ Add Transaction</button>
</div>

    <!-- TABLE -->
    <div class="card" style="flex:1;">
        <h3>Transactions</h3>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Date</th>
                
                    <th>Actions</th>
                    <th>From → To</th>
                    <th>Converted</th>
                    
                </tr>
            </thead>

            <tbody id="tableData"></tbody>
        </table>
    </div>

</div>
<div id="errorBox" style="
    display:none;
    background:#ffdddd;
    color:#a10000;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #ff5c5c;
    border-radius:6px;
"></div>

<script src="js/script.js"></script>

</body>
</html>
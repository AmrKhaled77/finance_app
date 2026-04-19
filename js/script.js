let editId = null;
let chart;

// ================= LOAD DATA =================
function loadData() {

    fetch("DB_Ops.php?action=get")
        .then(res => res.json())
        .then(data => {

            let rows = "";
            let income = 0;
            let expense = 0;

            // ================= EMPTY DATA HANDLING =================
            if (!data || data.length === 0) {

                rows = `
                <tr>
                    <td colspan="7" style="
                        text-align:center;
                        padding:30px;
                        color:#777;
                        font-size:16px;
                    ">
                        📭 No transactions found<br>
                        <small>Start by adding your first income or expense 💡</small>
                    </td>
                </tr>
                `;

                document.getElementById("tableData").innerHTML = rows;

                document.getElementById("incomeTotal").innerText = 0;
                document.getElementById("expenseTotal").innerText = 0;
                document.getElementById("balanceTotal").innerText = 0;

                renderChart(0, 0);

                return;
            }

            // ================= DATA LOOP =================
            data.forEach(item => {

                let amount = Number(item.amount) || 0;

                if (item.type === "income") income += amount;
                else expense += amount;

                rows += `
                <tr>
                    <td>${item.title}</td>
                    <td>${amount}</td>
                    <td>${item.type}</td>
                    <td>${item.category}</td>
                    <td>${item.date}</td>

                    <td>
                        <button class="btn-convert" data-id="${item.id}" data-amount="${amount}">💱</button>
                        <button class="btn-edit" data-item='${JSON.stringify(item)}'>✏️</button>
                        <button class="btn-delete" data-id="${item.id}">❌</button>
                    </td>

                    <td id="converted-${item.id}">—</td>
                </tr>
                `;
            });

            // ================= RENDER TABLE =================
            document.getElementById("tableData").innerHTML = rows;

            // ================= UPDATE STATS =================
            document.getElementById("incomeTotal").innerText = income;
            document.getElementById("expenseTotal").innerText = expense;

            const balance = income - expense;
            document.getElementById("balanceTotal").innerText = balance.toFixed(2);

            // ================= EVENTS + CHART =================
            attachTableEvents();
            renderChart(income, expense);
        })
        .catch(err => {
            console.error("Error loading data:", err);

            document.getElementById("tableData").innerHTML = `
                <tr>
                    <td colspan="7" style="text-align:center; color:red;">
                        ⚠️ Failed to load data
                    </td>
                </tr>
            `;

            renderChart(0, 0);
        });
}


// ================= ATTACH EVENTS =================
function attachTableEvents() {

    // EDIT
    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.addEventListener("click", () => {
            const item = JSON.parse(btn.dataset.item);
            startEdit(
                item.id,
                item.title,
                item.amount,
                item.type,
                item.category,
                item.date
            );
        });
    });

    // DELETE
    document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.addEventListener("click", () => {
            deleteTransaction(btn.dataset.id);
        });
    });

    // CONVERT
    document.querySelectorAll(".btn-convert").forEach(btn => {
        btn.addEventListener("click", () => {
            convertCurrency(btn.dataset.amount, btn.dataset.id);
        });
    });
}


// ================= ADD / UPDATE =================
function addTransaction() {

    let formData = new FormData();

    const title = document.getElementById("title").value;
    const amount = document.getElementById("amount").value;
    const type = document.getElementById("type").value;
    const category = document.getElementById("category").value;
    const date = document.getElementById("date").value;

    formData.append("action", editId === null ? "add" : "update");

    if (editId !== null) {
        formData.append("id", editId);
    }

    formData.append("title", title);
    formData.append("amount", amount);
    formData.append("type", type);
    formData.append("category", category);
    formData.append("date", date);

    fetch("DB_Ops.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {

            alert(data.message);

            editId = null;
            document.getElementById("submitBtn").innerText = "+ Add Transaction";

            resetForm();

            loadData();
        });
}


// ================= RESET FORM =================
function resetForm() {
    document.getElementById("title").value = "";
    document.getElementById("amount").value = "";
    document.getElementById("category").value = "";
    document.getElementById("date").value = "";
}


// ================= START EDIT =================
function startEdit(id, title, amount, type, category, date) {

    editId = id;

    document.getElementById("title").value = title;
    document.getElementById("amount").value = amount;
    document.getElementById("type").value = type;
    document.getElementById("category").value = category;
    document.getElementById("date").value = date;

    document.getElementById("submitBtn").innerText = "Update Transaction";
}


// ================= DELETE =================
function deleteTransaction(id) {

    let formData = new FormData();
    formData.append("action", "delete");
    formData.append("id", id);

    fetch("DB_Ops.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadData();
        });
}


// ================= CONVERT CURRENCY =================
function convertCurrency(amount, id) {

    fetch(`API_Ops.php?from=EGP&to=USD&amount=${encodeURIComponent(amount)}`)
        .then(res => res.json())
        .then(data => {

            const cell = document.getElementById("converted-" + id);

            if (data.status === "success") {
                cell.innerText = "$ " + data.result;
            } else {
                cell.innerText = "Error";
            }
        });
}


// ================= CHART =================
function renderChart(income, expense) {

    const ctx = document.getElementById("financeChart").getContext("2d");

    const isEmpty = income === 0 && expense === 0;

    // ================= EMPTY STATE =================
    if (isEmpty) {

        if (chart) {
            chart.destroy();
            chart = null;
        }

        // Optional: show message instead of chart
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

        ctx.font = "16px Arial";
        ctx.fillStyle = "#888";
        ctx.textAlign = "center";
        ctx.fillText("No data to display 📭", ctx.canvas.width / 2, ctx.canvas.height / 2);

        return;
    }

    // ================= CREATE CHART =================
    if (!chart) {

        chart = new Chart(ctx, {
            type: "doughnut",

            data: {
                labels: ["Income", "Expense"],
                datasets: [{
                    data: [income, expense],
                    backgroundColor: ["#2ecc71", "#e74c3c"],
                    borderWidth: 3
                }]
            },

            options: {
                responsive: true,
                cutout: "60%",

                plugins: {
                    legend: {
                        position: "bottom"
                    }
                }
            }
        });

    } else {

        // ================= UPDATE CHART =================
        chart.data.datasets[0].data = [income, expense];
        chart.update();
    }
}


// ================= INIT =================
loadData();
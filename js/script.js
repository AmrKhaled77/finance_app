let editId = null;

// ================= LOAD DATA =================
function loadData() {

    fetch("DB_Ops.php?action=get")
    .then(res => res.json())
    .then(data => {

        let rows = "";
        let income = 0;
        let expense = 0;

        data.forEach(item => {

            let amount = parseFloat(item.amount);

            if (item.type === "income") income += amount;
            else expense += amount;

            rows += `
                <tr>
                    <td>${item.title}</td>
                    <td>${item.amount}</td>
                    <td>${item.type}</td>
                    <td>${item.category}</td>
                    <td>${item.date}</td>
                    <td>
                        <button class="btn-convert" onclick="convertCurrency(${item.amount})">💱</button>

                        <button class="btn-edit" onclick="startEdit(
                            ${item.id},
                            '${item.title}',
                            ${item.amount},
                            '${item.type}',
                            '${item.category}',
                            '${item.date}'
                        )">✏️</button>

                        <button class="btn-delete" onclick="deleteTransaction(${item.id})">❌</button>
                    </td>
                </tr>
            `;
        });

        document.getElementById("tableData").innerHTML = rows;

        document.getElementById("incomeTotal").innerText = income;
        document.getElementById("expenseTotal").innerText = expense;
        document.getElementById("balanceTotal").innerText = income - expense;

        // ✅ MUST BE INSIDE THEN
        renderChart(income, expense);
    });
}


// ================= ADD / UPDATE =================
function addTransaction() {

    let formData = new FormData();

    let title = document.getElementById("title").value;
    let amount = document.getElementById("amount").value;
    let type = document.getElementById("type").value;
    let category = document.getElementById("category").value;
    let date = document.getElementById("date").value;

    if (editId === null) {
        formData.append("action", "add");
    } else {
        formData.append("action", "update");
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

        document.getElementById("title").value = "";
        document.getElementById("amount").value = "";
        document.getElementById("category").value = "";
        document.getElementById("date").value = "";

        loadData();
    });
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


// ================= CONVERT API =================
function convertCurrency(amount) {

    fetch(`API_Ops.php?from=EGP&to=USD&amount=${amount}`)
    .then(res => res.json())
    .then(data => {

        if (data.status === "success") {
            alert("💱 USD: " + data.result);
        } else {
            alert("Error");
        }
    });
}


// INIT
loadData();



let chart;

function renderChart(income, expense) {

    const ctx = document.getElementById("financeChart").getContext("2d");

    if (chart) {
        chart.destroy();
    }

    chart = new Chart(ctx, {
        type: "doughnut", // 👈 better than pie (more modern look)

        data: {
            labels: ["Income", "Expense"],
            datasets: [{
                data: [income, expense],

                backgroundColor: [
                    "#2ecc71",
                    "#e74c3c"
                ],

                borderColor: "#ffffff",
                borderWidth: 3,
                hoverOffset: 15
            }]
        },

        options: {
            responsive: true,
            cutout: "60%", // 👈 makes it modern donut style

            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: "#2c3e50",
                        font: {
                            size: 14,
                            weight: "bold"
                        },
                        padding: 20
                    }
                },

                tooltip: {
                    backgroundColor: "#2c3e50",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    padding: 10
                }
            }
        }
    });
}
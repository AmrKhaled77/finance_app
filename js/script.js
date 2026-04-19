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

            if (!data || data.length === 0) {

                rows = `
                <tr>
                    <td colspan="7" style="text-align:center; padding:30px; color:#777;">
                        📭 No transactions found
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
                        <button class="btn-edit" data-item='${JSON.stringify(item)}'>✏️</button>
                        <button class="btn-delete" data-id="${item.id}">❌</button>
                        <button class="btn-convert" data-id="${item.id}" data-amount="${amount}">💱</button>
                    </td>
                    <td id="converted-${item.id}">—</td>
                </tr>
                `;
            });

            document.getElementById("tableData").innerHTML = rows;

            document.getElementById("incomeTotal").innerText = income;
            document.getElementById("expenseTotal").innerText = expense;
            document.getElementById("balanceTotal").innerText = (income - expense).toFixed(2);

            attachTableEvents();
            renderChart(income, expense);
        });
}

// ================= ERROR HANDLING =================
function setError(id, msg) {
    document.getElementById(id + "Error").innerText = msg;
}

function clearErrors() {
    ["title", "amount", "type", "category", "date"].forEach(id => {
        document.getElementById(id + "Error").innerText = "";
    });
}

// ================= LIVE VALIDATION CLEAR =================
function attachLiveValidation() {

    ["title", "amount", "type", "category", "date"].forEach(id => {

        const el = document.getElementById(id);

        el.addEventListener("input", () => {
            setError(id, "");
        });

        el.addEventListener("change", () => {
            setError(id, "");
        });
    });
}

// ================= ADD / UPDATE =================
function addTransaction() {

    clearErrors();

    let isValid = true;

    const title = document.getElementById("title").value.trim();
    const amount = document.getElementById("amount").value.trim();
    const type = document.getElementById("type").value;
    const category = document.getElementById("category").value.trim();
    const date = document.getElementById("date").value;

    // TITLE
    if (!title) {
        setError("title", "Title is required");
        isValid = false;
    } else if (title.length < 3) {
        setError("title", "Minimum 3 characters");
        isValid = false;
    }

    // AMOUNT
    if (!amount) {
        setError("amount", "Amount is required");
        isValid = false;
    } else if (isNaN(amount) || Number(amount) <= 0) {
        setError("amount", "Must be > 0");
        isValid = false;
    }

    // TYPE
    if (!type) {
        setError("type", "Select type");
        isValid = false;
    }

    // CATEGORY
    if (!category) {
        setError("category", "Category required");
        isValid = false;
    }

    // DATE
    if (!date) {
        setError("date", "Date required");
        isValid = false;
    } else {
        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate > today) {
            setError("date", "Future date not allowed");
            isValid = false;
        }
    }

    if (!isValid) return;

    let formData = new FormData();

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

// ================= RESET =================
function resetForm() {
    document.getElementById("title").value = "";
    document.getElementById("amount").value = "";
    document.getElementById("type").value = "";
    document.getElementById("category").value = "";
    document.getElementById("date").value = "";
}

// ================= TABLE EVENTS =================
function attachTableEvents() {

    document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.onclick = () => deleteTransaction(btn.dataset.id);
    });

    document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.onclick = () => {
            const item = JSON.parse(btn.dataset.item);
            editId = item.id;

            document.getElementById("title").value = item.title;
            document.getElementById("amount").value = item.amount;
            document.getElementById("type").value = item.type;
            document.getElementById("category").value = item.category;
            document.getElementById("date").value = item.date;

            document.getElementById("submitBtn").innerText = "Update Transaction";
        };
    });
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

// ================= INIT =================
loadData();
attachLiveValidation();
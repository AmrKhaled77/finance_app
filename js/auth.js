document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");
    const showSignupBtn = document.getElementById("showSignup");
    const showLoginBtn = document.getElementById("showLogin");
    const authMessage = document.getElementById("authMessage");

    if (showSignupBtn) {
        showSignupBtn.addEventListener("click", (e) => {
            e.preventDefault();
            loginForm.classList.add("hidden");
            signupForm.classList.remove("hidden");
            authMessage.classList.add("hidden");
            signupForm.reset();
        });
    }

    if (showLoginBtn) {
        showLoginBtn.addEventListener("click", (e) => {
            e.preventDefault();
            signupForm.classList.add("hidden");
            loginForm.classList.remove("hidden");
            authMessage.classList.add("hidden");
            loginForm.reset();
        });
    }

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            formData.append("action", "login");
            processAuth(formData, this.querySelector('button[type="submit"]'));
        });
    }

    if (signupForm) {
        signupForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const fileInput = document.getElementById('profilePicInput');

            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 2 * 1024 * 1024;

                if (file.size > maxSize) {
                    authMessage.classList.remove("hidden");
                    authMessage.innerText = "Error: Profile picture must be under 2MB.";
                    authMessage.className = "mt-4 p-3 rounded-lg text-sm text-center font-medium bg-red-100 text-red-700 block";
                    return;
                }

                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    authMessage.classList.remove("hidden");
                    authMessage.innerText = "Error: Only JPG and PNG image files are allowed.";
                    authMessage.className = "mt-4 p-3 rounded-lg text-sm text-center font-medium bg-red-100 text-red-700 block";
                    return;
                }
            }
            // ----------------------------------------

            let formData = new FormData(this);
            formData.append("action", "signup");
            processAuth(formData, this.querySelector('button[type="submit"]'));
        });
    }

    function processAuth(formData, submitBtn) {
        const originalBtnText = submitBtn.innerText;
        submitBtn.innerText = "Processing...";
        submitBtn.disabled = true;

        fetch("auth_api.php", {
            method: "POST", body: formData
        }).then(res => {
            if (!res.ok) throw new Error("Network response was not ok");
            return res.json();
        }).then(data => {
            submitBtn.innerText = originalBtnText;
            submitBtn.disabled = false;

            authMessage.classList.remove("hidden");
            authMessage.innerText = data.message;

            if (data.status === "success") {
                authMessage.className = "mt-4 p-3 rounded-lg text-sm text-center font-medium bg-green-100 text-green-700";

                if (formData.get("action") === "login") {
                    setTimeout(() => {
                        window.location.href = "dashboard.php";
                    }, 800);
                } else {
                    setTimeout(() => {
                        document.getElementById("showLogin").click();
                    }, 1500);
                }
            } else {
                authMessage.className = "mt-4 p-3 rounded-lg text-sm text-center font-medium bg-red-100 text-red-700";
            }
        }).catch(error => {
            console.error("Error:", error);
            submitBtn.innerText = originalBtnText;
            submitBtn.disabled = false;

            authMessage.classList.remove("hidden");
            authMessage.innerText = "An error occurred. Please try again.";

            authMessage.className = "mt-4 p-3 rounded-lg text-sm text-center font-medium bg-red-100 text-red-700";
        })
    }
})
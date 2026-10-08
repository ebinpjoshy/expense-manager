/**
 * Client-Side Form Validation
 * Personal Expense Management System
 */

document.addEventListener("DOMContentLoaded", function () {
    // -------------------------------------------------------------
    // Helper Functions for Validation UI
    // -------------------------------------------------------------
    function setError(input, message) {
        if (!input) return;
        input.classList.add("is-invalid");

        let errorEl = input.parentElement.querySelector(".invalid-feedback");
        if (!errorEl) {
            errorEl = document.createElement("div");
            errorEl.className = "invalid-feedback";
            input.parentElement.appendChild(errorEl);
        }
        errorEl.textContent = message;
        errorEl.style.display = "block";
    }

    function clearError(input) {
        if (!input) return;
        input.classList.remove("is-invalid");
        const errorEl = input.parentElement.querySelector(".invalid-feedback");
        if (errorEl) {
            errorEl.textContent = "";
            errorEl.style.display = "none";
        }
    }

    // -------------------------------------------------------------
    // 1. Registration Form Validation
    // -------------------------------------------------------------
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function (e) {
            let isValid = true;

            const nameInput = document.getElementById("reg_name");
            const emailInput = document.getElementById("reg_email");
            const phoneInput = document.getElementById("reg_phone");
            const passwordInput = document.getElementById("reg_password");
            const confirmInput = document.getElementById("reg_confirm_password");

            // Name validation
            if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
                setError(nameInput, "Please enter your full name (minimum 2 characters).");
                isValid = false;
            } else {
                clearError(nameInput);
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value.trim())) {
                setError(emailInput, "Please enter a valid email address (e.g., student@college.edu).");
                isValid = false;
            } else {
                clearError(emailInput);
            }

            // Phone validation: exactly 10 digits
            const phoneRegex = /^[0-9]{10}$/;
            const cleanedPhone = phoneInput.value.trim().replace(/\D/g, "");
            if (!phoneRegex.test(cleanedPhone)) {
                setError(phoneInput, "Please enter a valid 10-digit mobile number.");
                isValid = false;
            } else {
                clearError(phoneInput);
            }

            // Password complexity validation:
            // Min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char
            const passwordVal = passwordInput.value;
            const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
            if (!strongPasswordRegex.test(passwordVal)) {
                setError(passwordInput, "Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character.");
                isValid = false;
            } else {
                clearError(passwordInput);
            }

            // Confirm Password validation
            if (confirmInput.value !== passwordVal) {
                setError(confirmInput, "Passwords do not match.");
                isValid = false;
            } else {
                clearError(confirmInput);
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // -------------------------------------------------------------
    // 2. Login Form Validation
    // -------------------------------------------------------------
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            let isValid = true;
            const emailInput = document.getElementById("login_email");
            const passwordInput = document.getElementById("login_password");

            if (!emailInput.value.trim()) {
                setError(emailInput, "Email address is required.");
                isValid = false;
            } else {
                clearError(emailInput);
            }

            if (!passwordInput.value) {
                setError(passwordInput, "Password is required.");
                isValid = false;
            } else {
                clearError(passwordInput);
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // -------------------------------------------------------------
    // 3. Transaction Form Validation (Income / Expense / Edit)
    // -------------------------------------------------------------
    const txnForm = document.getElementById("transactionForm");
    if (txnForm) {
        txnForm.addEventListener("submit", function (e) {
            let isValid = true;
            const amountInput = document.getElementById("amount");
            const categoryInput = document.getElementById("category_id");
            const paymentInput = document.getElementById("payment_method");
            const dateInput = document.getElementById("transaction_date");

            // Amount validation
            const amountVal = parseFloat(amountInput.value);
            if (isNaN(amountVal) || amountVal <= 0) {
                setError(amountInput, "Amount must be a positive number greater than 0.");
                isValid = false;
            } else {
                clearError(amountInput);
            }

            // Category validation
            if (!categoryInput.value || categoryInput.value === "") {
                setError(categoryInput, "Please select a category.");
                isValid = false;
            } else {
                clearError(categoryInput);
            }

            // Payment method validation
            if (!paymentInput.value || paymentInput.value === "") {
                setError(paymentInput, "Please select a payment method.");
                isValid = false;
            } else {
                clearError(paymentInput);
            }

            // Date validation
            if (!dateInput.value) {
                setError(dateInput, "Please select a valid transaction date.");
                isValid = false;
            } else {
                clearError(dateInput);
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // -------------------------------------------------------------
    // 4. Budget Form Validation
    // -------------------------------------------------------------
    const budgetForm = document.getElementById("budgetForm");
    if (budgetForm) {
        budgetForm.addEventListener("submit", function (e) {
            let isValid = true;
            const catInput = document.getElementById("budget_category");
            const amountInput = document.getElementById("budget_amount");

            if (!catInput.value) {
                setError(catInput, "Please choose an expense category.");
                isValid = false;
            } else {
                clearError(catInput);
            }

            const amountVal = parseFloat(amountInput.value);
            if (isNaN(amountVal) || amountVal <= 0) {
                setError(amountInput, "Monthly budget amount must be greater than 0.");
                isValid = false;
            } else {
                clearError(amountInput);
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

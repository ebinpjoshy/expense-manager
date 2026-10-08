/**
 * General UI Interactions and Dynamic Helpers
 * ExpenseMgr — Personal Financial Intelligence System
 */

document.addEventListener("DOMContentLoaded", function () {
    // -------------------------------------------------------------
    // 1. Mobile & Tablet Sidebar Drawer Toggle
    // -------------------------------------------------------------
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebarBackdrop = document.getElementById("sidebarBackdrop");

    function openSidebar() {
        if (sidebar) sidebar.classList.add("open");
        if (sidebarBackdrop) sidebarBackdrop.classList.add("active");
        document.body.classList.add("sidebar-locked");
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove("open");
        if (sidebarBackdrop) sidebarBackdrop.classList.remove("active");
        document.body.classList.remove("sidebar-locked");
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            if (sidebar && sidebar.classList.contains("open")) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener("click", function () {
            closeSidebar();
        });
    }

    // Close sidebar when clicking any navigation link on small screens
    const sidebarLinks = document.querySelectorAll(".sidebar-menu a");
    sidebarLinks.forEach(function (link) {
        link.addEventListener("click", function () {
            if (window.innerWidth <= 992) {
                closeSidebar();
            }
        });
    });

    // -------------------------------------------------------------
    // 2. Custom Date Range Toggle on Dashboard / Reports
    // -------------------------------------------------------------
    const customDateBox = document.getElementById("customDateRangeBox");

    window.toggleCustomDateRange = function (show) {
        if (customDateBox) {
            if (show) {
                customDateBox.style.display = "flex";
                const startInput = document.getElementById("custom_start_date");
                if (startInput) startInput.focus();
            } else {
                customDateBox.style.display = "none";
            }
        }
    };

    // -------------------------------------------------------------
    // 3. Auto Dismiss Alerts after 5 seconds
    // -------------------------------------------------------------
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = "opacity 0.4s ease, transform 0.4s ease";
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-8px)";
            setTimeout(function () {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, 400);
        }, 5000);
    });
});

/**
 * Global Delete Confirmation Dialog
 * Invoked by onclick attributes on delete buttons
 *
 * @param {Event} event
 * @param {string} customMsg
 * @returns {boolean}
 */
function confirmDelete(event, customMsg) {
    const message = customMsg || "Are you sure you want to delete this record? This action cannot be undone.";
    if (!confirm(message)) {
        if (event) {
            event.preventDefault();
        }
        return false;
    }
    return true;
}

// =====================================
// secret-admin.js - Hidden admin access
// =====================================

document.addEventListener("DOMContentLoaded", function () {

    const star     = document.getElementById("secret-star");
    const adminBtn = document.getElementById("admin-reveal-button");

    if (!star || !adminBtn) {
        console.warn("secret-admin.js: star or button not found.");
        return;
    }

    let clickCount   = 0;
    let timeoutReset = null;

    star.addEventListener("click", function () {

        // Reset timer each click
        clearTimeout(timeoutReset);
        clickCount++;

        // Reset count if user pauses too long
        timeoutReset = setTimeout(() => {
            clickCount = 0;
        }, 2000);

        if (clickCount >= 5) {
            adminBtn.style.display = "block";
            clickCount = 0;
        }
    });

    adminBtn.addEventListener("click", function () {
        window.location.href = "/admin/admin-login.php";
    });

});

// secret-admin.js
document.addEventListener("DOMContentLoaded", function () {

    const star = document.getElementById("secret-star");
    const adminBtn = document.getElementById("admin-reveal-button");

    let clickCount = 0;
    let timeoutReset;

    star.addEventListener("click", function () {

        clearTimeout(timeoutReset);
        clickCount++;

        // Reset after 2 seconds of inactivity
        timeoutReset = setTimeout(() => clickCount = 0, 2000);

        if (clickCount >= 5) {
            adminBtn.style.display = "block";
            clickCount = 0;
        }
    });

    adminBtn.addEventListener("click", function () {
        // ✔ Correct admin login path
        window.location.href = "/groupproject/Version-035/admin/admin-login.php";
    });

});

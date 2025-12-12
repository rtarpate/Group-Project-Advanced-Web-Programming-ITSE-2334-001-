<?php
// ============================================================
// footer.php — Shared site footer
// ============================================================
?>
</main>

<footer>
    <p>&copy; <?= date("Y"); ?> Star Media Review. All rights reserved.</p>
</footer>

<!-- Hidden admin access icon + button -->
<div id="secret-star-container">
    <!-- Use whatever image you want as the secret icon -->
    <img id="secret-star"
         src="/assets/images/0.jpg"
         alt="Secret admin access">
</div>

<button id="admin-reveal-button" type="button">
    Admin Login
</button>

<!-- JS that listens for 5 clicks on the star and reveals the button -->
<script src="/assets/javascript/secret-admin.js"></script>

</body>
</html>

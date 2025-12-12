<?php
// pages/request-new-media.php
// Header & footer handled by index.php
?>

<h1>Request New Media</h1>

<form id="requestMediaForm" method="POST" action="/router/router.php?action=requestMedia" class="request-form">
    <label for="mediaName">Media Name *</label>
    <input id="mediaName" type="text" name="media_name" required>

    <label for="mediaType">Type *</label>
    <select id="mediaType" name="media_type" required>
        <option value="">Select Type</option>
        <!-- populated by load-media-types.js -->
    </select>

    <label for="mediaDesc">Description *</label>
    <textarea id="mediaDesc" name="description" required></textarea>

    <button type="submit">Submit</button>

    <p id="requestStatus" class="form-status" style="display:none;"></p>
</form>

<script src="/assets/javascript/load-media-types.js"></script>
<script src="/assets/javascript/form-request-new-media-ajax.js"></script>

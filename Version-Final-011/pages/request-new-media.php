<?php
// pages/request-new-media.php
// Header & footer are handled by index.php
?>

<h1>Request New Media</h1>

<form method="POST" action="/router/router.php?action=requestMedia" class="request-form">
    <label>Media Name *</label>
    <input type="text" name="media_name" required>

    <label>Type *</label>
    <select name="media_type" required>
        <option value="">Select Type</option>
        <option value="Movie">Movie</option>
        <option value="TV Show">TV Show</option>
        <option value="Manga">Manga</option>
        <option value="Novel">Novel</option>
        <option value="Game">Game</option>
        <option value="Web Series">Web Series</option>
        <option value="Audio Book">Audio Book</option>
    </select>

    <label>Description *</label>
    <textarea name="description" required></textarea>

    <button type="submit">Submit</button>
</form>

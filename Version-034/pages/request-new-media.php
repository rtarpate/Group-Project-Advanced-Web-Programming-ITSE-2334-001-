<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <h1 class="page-title">Request New Media</h1>

    <p class="page-intro">
        Don&apos;t see something you&apos;d like to review? Suggest a new movie, show, game, book, or other media here.
    </p>

    <div id="form-message" class="form-message"></div>

    <form id="request-new-media-form" class="styled-form">
        <div class="form-row">
            <label for="media_name">Media Name <span class="required">*</span></label>
            <input type="text" name="media_name" id="media_name" required>
        </div>

        <div class="form-row">
            <label for="media_type">Type <span class="required">*</span></label>
            <select name="media_type" id="media_type" required>
                <option value="">-- Select Type --</option>
                <option value="Movie">Movie</option>
                <option value="TV Show">TV Show</option>
                <option value="Video Game">Video Game</option>
                <option value="Comic Book">Comic Book</option>
                <option value="Manga">Manga</option>
                <option value="Novel">Novel</option>
                <option value="Web Novel">Web Novel</option>
                <option value="Web Series">Web Series</option>
                <option value="Audio Book">Audio Book</option>
            </select>
        </div>

        <div class="form-row">
            <label for="media_description">Description <span class="required">*</span></label>
            <textarea name="media_description" id="media_description" rows="4" required></textarea>
        </div>

        <div class="form-row">
            <button type="submit" class="primary-btn">Submit Request</button>
        </div>
    </form>
</section>

<script src="../assets/javascript/form-request-new-media.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

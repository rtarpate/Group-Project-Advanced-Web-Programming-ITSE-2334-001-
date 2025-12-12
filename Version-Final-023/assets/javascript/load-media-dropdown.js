document.addEventListener('DOMContentLoaded', () => {
    console.log('load-media-dropdown.js loaded');

    const select = document.getElementById('media_id');

    if (!select) {
        console.warn('Media dropdown not found');
        return;
    }

    fetch('/includes/get-media.php')
        .then(response => response.json())
        .then(data => {
            console.log('Media data received:', data);

            select.innerHTML = '<option value="">-- Select Media --</option>';

            data.forEach(media => {
                const option = document.createElement('option');
                option.value = media.media_id;
                option.textContent = media.title;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Failed to load media list:', error);
        });
});

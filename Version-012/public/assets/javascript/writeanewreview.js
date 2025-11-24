// writeanewreview.js
// Handles populating the media dropdown, form validation/sanitization, and rendering reviews.
// Media list derived from the project's sample list (movies, TV, video games).
const mediaList = [
  // Movies
  { title: 'Star Wars: Episode VII - The Force Awakens', type: 'Movie' },
  { title: 'Avengers: Endgame', type: 'Movie' },
  { title: 'Spider-Man: No Way Home', type: 'Movie' },
  { title: 'Avatar', type: 'Movie' },
  { title: 'Top Gun: Maverick', type: 'Movie' },
  { title: 'Avengers: Infinity War', type: 'Movie' },
  { title: 'Titanic', type: 'Movie' },
  { title: 'The Avengers', type: 'Movie' },
  { title: 'The Dark Knight', type: 'Movie' },
  { title: 'The Matrix', type: 'Movie' },

  // TV
  { title: 'Breaking Bad', type: 'TV' },
  { title: 'Band of Brothers', type: 'TV' },
  { title: 'Chernobyl', type: 'TV' },
  { title: 'The Wire', type: 'TV' },
  { title: 'Avatar: The Last Airbender', type: 'TV' },
  { title: 'The Sopranos', type: 'TV' },
  { title: 'Game of Thrones', type: 'TV' },
  { title: 'Fullmetal Alchemist: Brotherhood', type: 'TV' },
  { title: 'Attack on Titan', type: 'TV' },
  { title: 'The Last Dance', type: 'TV' },
  { title: 'Rick and Morty', type: 'TV' },
  { title: 'Sherlock', type: 'TV' },
  { title: 'Better Call Saul', type: 'TV' },
  { title: 'The Office', type: 'TV' },
  { title: 'True Detective', type: 'TV' },

  // Video Games
  { title: 'Red Dead Redemption 2', type: 'Video Game' },
  { title: 'The Last of US', type: 'Video Game' },
  { title: 'Baldur’s Gate 3', type: 'Video Game' },
  { title: 'The Witcher 3: Wild Hunt', type: 'Video Game' },
  { title: 'The Legend of Zelda: Ocarina of Time', type: 'Video Game' },
  { title: 'Final Fantasy VII', type: 'Video Game' },
  { title: 'God of War', type: 'Video Game' },
  { title: 'God of War: Ragnarok', type: 'Video Game' },
  { title: 'Mass Effect 2', type: 'Video Game' },
  { title: 'Metal Gear Solid', type: 'Video Game' }
];

function escapeHtml(unsafe) {
  if (!unsafe) return '';
  return unsafe
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function getStoredReviews() {
  try {
    const s = localStorage.getItem('reviews');
    return s ? JSON.parse(s) : [];
  } catch (e) {
    console.error('Failed to parse stored reviews', e);
    return [];
  }
}

function saveStoredReviews(arr) {
  localStorage.setItem('reviews', JSON.stringify(arr));
}

document.addEventListener('DOMContentLoaded', function () {
  // Populate media dropdown if present
  const select = document.getElementById('media-title');
  if (select) {
    mediaList.forEach(m => {
      const opt = document.createElement('option');
      opt.value = m.title;
      opt.textContent = m.title;
      select.appendChild(opt);
    });

    // When selection changes, auto-fill type
    const typeInput = document.getElementById('media-type');
    select.addEventListener('change', function () {
      const found = mediaList.find(x => x.title === this.value);
      typeInput.value = found ? found.type : '';
    });

    // ensure initial value
    if (select.options.length > 0) {
      select.selectedIndex = 0;
      const ev = new Event('change');
      select.dispatchEvent(ev);
    }
  }

  // Handle form submission
  const form = document.getElementById('new-review-form');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const title = document.getElementById('media-title').value;
      const type = document.getElementById('media-type').value;
      const ratingEl = document.getElementById('rating');
      const reviewEl = document.getElementById('review-text');
      const messages = document.getElementById('form-messages');

      const rating = Number(ratingEl.value);
      const reviewRaw = reviewEl.value.trim();

      // Validation
      const errors = [];
      if (!title) errors.push('Please select a media title.');
      if (!type) errors.push('Unknown media type.');
      if (!Number.isInteger(rating) || rating < 1 || rating > 10) {
        errors.push('Rating must be an integer between 1 and 10.');
      }
      if (reviewRaw.length < 5) {
        errors.push('Review must be at least 5 characters.');
      }

      if (errors.length) {
        messages.innerHTML = '<p class="error">' + errors.join('<br>') + '</p>';
        return;
      }

      // Sanitization (escape HTML)
      const review = escapeHtml(reviewRaw);

      // Build review object
      const newReview = {
        id: Date.now(),
        title,
        type,
        rating,
        review
      };

      // Save to localStorage
      const all = getStoredReviews();
      // Replace existing review for same title by same user (simple rule) - remove previous with same title
      const filtered = all.filter(r => r.title !== title);
      filtered.unshift(newReview); // newest first
      saveStoredReviews(filtered);

      messages.innerHTML = '<p class="success">Review saved — go to Reviews to see it.</p>';

      // If we're on the reviews page in the same tab, render immediately by dispatching custom event
      window.dispatchEvent(new CustomEvent('reviews-updated'));

      // Optionally clear textarea and rating
      reviewEl.value = '';
      ratingEl.value = '';
    });
  }

  // When on reviews page: render stored + update sample review placeholders
  const container = document.getElementById('reviews-container');
  if (container) {
    function renderStored() {
      container.innerHTML = ''; // clear
      const all = getStoredReviews();
      for (const r of all) {
        const div = document.createElement('div');
        div.className = 'review-card';
        div.innerHTML = `
          <h3>${escapeHtml(r.title)}</h3>
          <p><strong>Type:</strong> ${escapeHtml(r.type)}</p>
          <p><strong>Rating:</strong> ${escapeHtml(String(r.rating))}/10</p>
          <p>${r.review}</p>
          <hr>
        `;
        container.appendChild(div);

        // Also update any sample review on page with same title
        const sample = document.querySelector('.sample-reviews [data-title="' + r.title + '"]');
        if (sample) {
          const rt = sample.querySelector('.review-text');
          if (rt) rt.innerHTML = r.review;
          const ratingEl = sample.querySelector('p strong + text');
          // update the rating paragraph manually
          const ratingPara = sample.querySelector('p');
          if (ratingPara) {
            // find the <p> that contains 'Rating:'; there are multiple <p>, so check text content
            const ps = sample.querySelectorAll('p');
            for (const p of ps) {
              if (p.textContent && p.textContent.trim().startsWith('Rating:')) {
                p.innerHTML = '<strong>Rating:</strong> ' + escapeHtml(String(r.rating)) + '/10';
              }
            }
          }
        }
      }
    }

    // initial render
    renderStored();

    // re-render if reviews updated in another tab or by form
    window.addEventListener('storage', renderStored);
    window.addEventListener('reviews-updated', renderStored);
  }
});

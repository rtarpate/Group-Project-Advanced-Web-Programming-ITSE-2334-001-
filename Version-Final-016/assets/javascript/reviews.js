document.addEventListener("DOMContentLoaded", () => {
  const container  = document.getElementById("mediaContainer");
  const searchIn   = document.getElementById("searchInput");
  const typeFilter = document.getElementById("typeFilter");
  const sortOrder  = document.getElementById("sortOrder");

  if (!container) return;

  let allMedia = [];

  function normalize(str) {
    return String(str || "").toLowerCase().trim();
  }

  function render() {
    const q    = normalize(searchIn?.value);
    const type = normalize(typeFilter?.value);
    const sort = sortOrder?.value || "title_asc";

    let list = allMedia.slice();

    if (q) {
      list = list.filter(m =>
        normalize(m.media_title).includes(q)
      );
    }

    // ✅ Only filter when a real type is selected
    if (type) {
      list = list.filter(m =>
        normalize(m.media_type) === type
      );
    }

    list.sort((a, b) => {
      const at = normalize(a.media_title);
      const bt = normalize(b.media_title);
      return sort === "title_desc"
        ? bt.localeCompare(at)
        : at.localeCompare(bt);
    });

    if (list.length === 0) {
      container.innerHTML = "<p>No media found.</p>";
      return;
    }

    container.innerHTML = "";
    list.forEach(item => {
      const img = item.media_image || "no-image.jpg";

      container.insertAdjacentHTML("beforeend", `
        <div class="media-card">
          <img src="/assets/images/${img}"
               onerror="this.onerror=null;this.src='/assets/images/no-image.jpg';">
          <div class="media-card-content">
            <h3>${item.media_title}</h3>
            <p><strong>Type:</strong> ${item.media_type}</p>
            <p><strong>Genre:</strong> ${item.genre_name || "N/A"}</p>
            <p><strong>Rating:</strong> ${Number(item.average_rating || 0).toFixed(1)}</p>
            <a class="details-link"
               href="/index.php?page=media-details&media_id=${item.media_id}">
               View details
            </a>
          </div>
        </div>
      `);
    });
  }

  searchIn?.addEventListener("input", render);
  typeFilter?.addEventListener("change", render);
  sortOrder?.addEventListener("change", render);

  fetch("/router/router.php?action=getMedia")
    .then(res => res.json())
    .then(data => {
      if (!data.success || !Array.isArray(data.media)) {
        container.innerHTML = "<p>Error loading media.</p>";
        return;
      }
      allMedia = data.media;
      render(); // ✅ render only AFTER data loads
    })
    .catch(() => {
      container.innerHTML = "<p>Error loading media.</p>";
    });
});

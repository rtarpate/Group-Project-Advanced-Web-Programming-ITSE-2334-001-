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
    const q = normalize(searchIn ? searchIn.value : "");
    const type = normalize(typeFilter ? typeFilter.value : "");
    const sort = sortOrder ? sortOrder.value : "title_asc";

    let list = allMedia.slice();

    if (q) list = list.filter(m => normalize(m.media_title).includes(q));
    if (type) list = list.filter(m => normalize(m.media_type) === type);

    list.sort((a, b) => {
      const at = normalize(a.media_title);
      const bt = normalize(b.media_title);
      if (sort === "title_desc") return bt.localeCompare(at);
      return at.localeCompare(bt);
    });

    if (list.length === 0) {
      container.innerHTML = "<p>No media found.</p>";
      return;
    }

    container.innerHTML = "";
    list.forEach(item => {
      const card = document.createElement("div");
      card.className = "media-card";

      const imgName = item.media_image || "no-image.jpg";
      const imgSrc  = "/assets/images/" + imgName;

      const avg = Number(item.average_rating || 0);
      const avgText = isFinite(avg) ? avg.toFixed(1) : "0.0";

      card.innerHTML = `
        <img src="${imgSrc}" alt="${item.media_title}" onerror="this.onerror=null;this.src='/assets/images/no-image.jpg';">
        <div class="media-card-content">
          <h3>${item.media_title}</h3>
          <p><strong>Type:</strong> ${item.media_type || "N/A"}</p>
          <p><strong>Genre:</strong> ${item.genre_name || "N/A"}</p>
          <p><strong>Rating:</strong> ${avgText}</p>
          <a class="details-link" href="/index.php?page=media-details&media_id=${item.media_id}">View details</a>
        </div>
      `;
      container.appendChild(card);
    });
  }

  if (searchIn) searchIn.addEventListener("input", render);
  if (typeFilter) typeFilter.addEventListener("change", render);
  if (sortOrder) sortOrder.addEventListener("change", render);

  fetch("/router/router.php?action=getMedia")
    .then(res => res.json())
    .then(data => {
      if (!data.success || !Array.isArray(data.media)) {
        container.innerHTML = "<p>Error loading media. Please try again later.</p>";
        return;
      }
      allMedia = data.media;
      render();
    })
    .catch(err => {
      console.error(err);
      container.innerHTML = "<p>Error loading media. Please try again later.</p>";
    });
});

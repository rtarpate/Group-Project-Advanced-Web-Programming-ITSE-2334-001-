document.addEventListener("DOMContentLoaded", () => {
  const select = document.getElementById("mediaSelect");
  if (!select) return;

  fetch("/router/router.php?action=getMediaTitles")
    .then(res => res.json())
    .then(data => {
      if (!data.success || !Array.isArray(data.titles)) return;

      data.titles.forEach(item => {
        const opt = document.createElement("option");
        opt.value = item.media_id;
        opt.textContent = item.title;
        select.appendChild(opt);
      });
    })
    .catch(err => console.error("load-media-dropdown error:", err));
});

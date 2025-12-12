document.addEventListener("DOMContentLoaded", () => {
  // Supports both:
  // 1) Request-new-media page: <select name="media_type">
  // 2) Reviews page: <select id="typeFilter">
  const requestSelect = document.querySelector("select[name='media_type']");
  const filterSelect  = document.getElementById("typeFilter");

  if (!requestSelect && !filterSelect) return;

  fetch("/router/router.php?action=getMediaTypes")
    .then(res => res.json())
    .then(data => {
      if (!data.success || !Array.isArray(data.types)) return;

      data.types.forEach(t => {
        const name = t.type_name;

        if (requestSelect) {
          const opt1 = document.createElement("option");
          opt1.value = name;
          opt1.textContent = name;
          requestSelect.appendChild(opt1);
        }

        if (filterSelect) {
          const opt2 = document.createElement("option");
          opt2.value = name;
          opt2.textContent = name;
          filterSelect.appendChild(opt2);
        }
      });
    })
    .catch(err => console.error("load-media-types error:", err));
});

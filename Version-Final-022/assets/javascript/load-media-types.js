document.addEventListener("DOMContentLoaded", () => {
  const requestSelect = document.querySelector("select[name='media_type']");
  const filterSelect  = document.getElementById("typeFilter");

  if (!requestSelect && !filterSelect) return;

  fetch("/router/router.php?action=getMediaTypes")
    .then(res => res.json())
    .then(data => {
      if (!data.success || !Array.isArray(data.types)) return;

      // ✅ Add default options
      if (requestSelect) {
        const opt = document.createElement("option");
        opt.value = "";
        opt.textContent = "Select Type";
        requestSelect.appendChild(opt);
      }

      if (filterSelect) {
        const opt = document.createElement("option");
        opt.value = "";
        opt.textContent = "All Types";
        filterSelect.appendChild(opt);
      }

      data.types.forEach(t => {
        const name = t.type_name;

        if (requestSelect) {
          const o = document.createElement("option");
          o.value = name;
          o.textContent = name;
          requestSelect.appendChild(o);
        }

        if (filterSelect) {
          const o = document.createElement("option");
          o.value = name;
          o.textContent = name;
          filterSelect.appendChild(o);
        }
      });
    })
    .catch(err => console.error("load-media-types error:", err));
});

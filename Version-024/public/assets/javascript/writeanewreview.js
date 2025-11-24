// Version-014: full working script (dropdown + auto-type + replace review using sessionStorage)
const mediaList = [
  { title: "Star Wars: Episode VII - The Force Awakens", type: "Movie" },
  { title: "Avengers: Endgame", type: "Movie" },
  { title: "Spider-Man: No Way Home", type: "Movie" },
  { title: "Avatar", type: "Movie" },
  { title: "Top Gun: Maverick", type: "Movie" },
  { title: "Avengers: Infinity War", type: "Movie" },
  { title: "Titanic", type: "Movie" },
  { title: "The Avengers", type: "Movie" },
  { title: "The Dark Knight", type: "Movie" },
  { title: "The Matrix", type: "Movie" },
  { title: "Breaking Bad", type: "TV Show" },
  { title: "Band of Brothers", type: "TV Show" },
  { title: "Chernobyl", type: "TV Show" },
  { title: "The Wire", type: "TV Show" },
  { title: "Avatar: The Last Airbender", type: "TV Show" },
  { title: "The Sopranos", type: "TV Show" },
  { title: "Game of Thrones", type: "TV Show" },
  { title: "Fullmetal Alchemist: Brotherhood", type: "TV Show" },
  { title: "Attack on Titan", type: "TV Show" },
  { title: "The Last Dance", type: "TV Show" },
  { title: "Rick and Morty", type: "TV Show" },
  { title: "Sherlock", type: "TV Show" },
  { title: "Better Call Saul", type: "TV Show" },
  { title: "The Office", type: "TV Show" },
  { title: "True Detective", type: "TV Show" },
  { title: "Red Dead Redemption 2", type: "Video Game" },
  { title: "The Last of Us", type: "Video Game" },
  { title: "Baldur’s Gate 3", type: "Video Game" },
  { title: "The Witcher 3: Wild Hunt", type: "Video Game" },
  { title: "The Legend of Zelda: Ocarina of Time", type: "Video Game" },
  { title: "Final Fantasy VII", type: "Video Game" },
  { title: "God of War", type: "Video Game" },
  { title: "God of War: Ragnarök", type: "Video Game" },
  { title: "Mass Effect 2", type: "Video Game" },
  { title: "Metal Gear Solid", type: "Video Game" },
];

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("new-review-form");
  const select = document.getElementById("media-title");
  const typeInput = document.getElementById("media-type");
  const ratingInput = document.getElementById("rating");
  const reviewText = document.getElementById("review-text");

  const sanitize = (s) => (s ? s.replace(/[<>]/g, "").trim() : "");
  const toStars = (r) => {
    r = Math.max(1, Math.min(10, parseInt(r) || 0));
    return "★".repeat(r) + "☆".repeat(10 - r);
  };

  // Populate dropdown
  if (select && typeInput) {
    mediaList.forEach((m) => {
      const opt = document.createElement("option");
      opt.value = m.title;
      opt.textContent = m.title;
      select.appendChild(opt);
    });
    select.addEventListener("change", () => {
      const chosen = mediaList.find((x) => x.title === select.value);
      typeInput.value = chosen ? chosen.type : "";
    });
    select.selectedIndex = 0;
    select.dispatchEvent(new Event("change"));
  }

  // Handle form submit
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const title = sanitize(select.value);
      const rating = parseInt(ratingInput.value);
      const review = sanitize(reviewText.value);
      if (!title || isNaN(rating) || !review) return;
      sessionStorage.setItem("tempReview", JSON.stringify({ title, rating, review }));
    });
  }

  // On reviews page: replace review text and stars
  if (window.location.pathname.includes("reviews.html")) {
    const saved = sessionStorage.getItem("tempReview");
    if (!saved) return;
    const { title, rating, review } = JSON.parse(saved);
    document.querySelectorAll(".review").forEach((r) => {
      const h3 = r.querySelector("h3");
      if (h3 && h3.textContent.trim().toLowerCase() === title.toLowerCase()) {
        const star = r.querySelector(".star");
        if (star) star.textContent = toStars(rating);
        const ps = r.querySelectorAll("p");
        if (ps.length) ps[ps.length - 1].textContent = review;
      }
    });
  }
});

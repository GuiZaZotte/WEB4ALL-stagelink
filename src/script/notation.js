document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".stars").forEach((starsContainer) => {
    const stars = starsContainer.querySelectorAll(".star");
    const entrepriseId = starsContainer.dataset.entrepriseId;
    const userRating = starsContainer.dataset.rating;

    // Initialiser les étoiles avec la note de l'utilisateur si elle existe
    if (userRating) {
      stars.forEach((s) => {
        if (s.dataset.value <= userRating) {
          s.classList.remove("far");
          s.classList.add("fas");
        } else {
          s.classList.remove("fas");
          s.classList.add("far");
        }
      });
    }

    stars.forEach((star) => {
      // Gestion du survol
      star.addEventListener("mouseover", function () {
        const currentRating = starsContainer.getAttribute("data-rating");
        if (currentRating) return; // Ne rien faire si une note est déjà validée

        const rating = this.dataset.value;
        stars.forEach((s) => {
          if (s.dataset.value <= rating) {
            s.classList.remove("far");
            s.classList.add("fas");
          } else {
            s.classList.remove("fas");
            s.classList.add("far");
          }
        });
      });

      // Rétablir l'affichage initial quand la souris quitte la zone
      starsContainer.addEventListener("mouseleave", function () {
        const currentRating = this.getAttribute("data-rating");
        if (currentRating) return; // Ne rien faire si une note est déjà validée

        stars.forEach((s) => {
          s.classList.remove("fas");
          s.classList.add("far");
        });
      });

      // Gestion du clic
      star.addEventListener("click", function () {
        if (!confirm("Voulez-vous vraiment noter cette entreprise ?")) return;

        const rating = this.dataset.value;
        fetch("rate_entreprise.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            entreprise_id: entrepriseId,
            note: rating,
          }),
          credentials: "same-origin",
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              starsContainer.setAttribute("data-rating", rating);
              const avisCount =
                starsContainer.parentElement.querySelector(".avis-count");
              avisCount.textContent = data.nombre_avis + " avis";

              // Mettre à jour les étoiles
              stars.forEach((s) => {
                if (s.dataset.value <= data.moyenne) {
                  s.classList.remove("far");
                  s.classList.add("fas");
                } else {
                  s.classList.remove("fas");
                  s.classList.add("far");
                }
              });

              alert("Merci pour votre évaluation !");
            } else {
              throw new Error(data.message || "Erreur lors de l'évaluation");
            }
          })
          .catch((error) => {
            console.error("Erreur:", error);
            alert("Une erreur est survenue lors de l'évaluation");
          });
      });
    });
  });
});

// assets/main.js
document.querySelectorAll(".reply-toggle").forEach((btn) => {
    btn.addEventListener("click", () => {
        const target = document.getElementById(btn.dataset.target);
        if (target) target.classList.toggle("hidden");
    });
});

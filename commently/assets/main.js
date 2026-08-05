// assets/main.js

function initCommentHandlers() {
    // 返信スレッドの表示・非表示
    document.querySelectorAll(".reply-toggle").forEach((btn) => {
        if (btn.dataset.initialized) return;
        btn.dataset.initialized = "true";

        btn.addEventListener("click", () => {
            const target = document.getElementById(btn.dataset.target);
            if (target) {
                target.classList.toggle("hidden");
            }
        });
    });

    // コメントフォームの表示・非表示
    const toggleBtn = document.getElementById("comment-toggle");
    const commentForm = document.getElementById("comment-form");

    if (toggleBtn && commentForm) {
        if (!toggleBtn.dataset.initialized) {
            toggleBtn.dataset.initialized = "true";
            toggleBtn.addEventListener("click", () => {
                commentForm.classList.toggle("hidden");

                if (commentForm.classList.contains("hidden")) {
                    toggleBtn.style.display = "inline-block";
                    toggleBtn.textContent = "💬 コメントを書く";
                } else {
                    toggleBtn.style.display = "none";
                    const textarea = commentForm.querySelector("textarea");
                    if (textarea) textarea.focus();
                }
            });
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    initCommentHandlers();
});

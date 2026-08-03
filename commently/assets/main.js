// assets/main.js

// 返信の表示・非表示
document.querySelectorAll(".reply-toggle").forEach((btn) => {
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
    toggleBtn.addEventListener("click", () => {

        commentForm.classList.toggle("hidden");

        if (commentForm.classList.contains("hidden")) {
            toggleBtn.textContent = "💬 コメントを書く";
        } else {
            toggleBtn.textContent = "✖ コメント入力を閉じる";
            commentForm.querySelector("textarea").focus();
        }

    });
}

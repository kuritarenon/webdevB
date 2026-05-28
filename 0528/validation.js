//関数の定義（JavaScript）
function validateForm(event) {
    const email = document.getElementById('email').value;
    const emailConfirm = document.getElementById('email_confirm').value;
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;

    //入力されているかを判定する
    if (
        email === '' ||
        emailConfirm === '' ||
        password === '' ||
        passwordConfirm === ''
    ) {
        alert('必須項目をすべて入力してください。');
        //送信ボタンを押すと、action先にデータが送信されるデフォルトを解除する関数
        event.preventDefault();
        return false;
    }

    //emailの1回目記述と再記述の整合性を判定
    if (email !== emailConfirm) {
        alert('メールアドレスが一致しません。');
        event.preventDefault();
        return false;
    }

    //パスワードの整合性を判定
    if (password !== passwordConfirm) {
        alert('パスワードが一致しません。');
        event.preventDefault();
        return false;
    }

    return true;
}
// DOM(htmlファイル)Content(中身)Loaded(読み込まれたら)というイベント
window.addEventListener('DOMContentLoaded', function () {
    //const formBtn=document .getElementById('registration-form');
    //formBtn.addEventListener('submit', validateForm);
    // sabmitはイベント、validateFormは関数名　＊callback関数
    document
        .getElementById('registration-form')
        .addEventListener('submit', validateForm);
});

// htmlが読み込まれたら、バリデーション関数が有効になる
// なぜ読み込まれてから、発動するようになっているのか

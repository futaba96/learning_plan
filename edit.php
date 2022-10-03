<?php
require_once __DIR__ . '/functions.php';

// index.php から渡された id を受け取る
$id = filter_input(INPUT_GET, 'id');

// 受け取った id のレコードを取得
$task = find_by_id($id);

/* タスク更新処理
---------------------------------------------*/
// 初期化
$title = '';
$due_date = '';
$errors = [];

// リクエストメソッドの判定
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームに入力されたデータを受け取る
    $title = filter_input(INPUT_POST, 'title');
    $due_date = filter_input(INPUT_POST, 'due_date');

    // バリデーション
    $errors = validate_tasks($title, $due_date, $task);

    // エラーチェック
    if (empty($errors)) {
        // タスク更新処理の実行
        update_task($id, $title, $due_date);

        // index.php にリダイレクト
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<?php include_once __DIR__ . '/_head.html' ?>

<body>
    <div class="wrapper">
        <h1 class="title">学習管理アプリ</h1>
        <!-- 編集ボタンで飛んだページ(編集エリア))-->
        <div class="form-area">
            <h2 class="sub-title">編集</h2>

            <?php if ($errors) echo (create_err_msg($errors)) ?>

            <form action="" method="post">
                <label for="title">学習内容</label>
                <!-- 登録されている内容を初期値としてフォームにセット-->
                <input type="text" name="title" value="<?= h($task['title']) ?>">
                <label for="due_date">期限日</label>
                <!-- 登録されている内容を初期値としてフォームにセット-->
                <input type="date" name="due_date" value="<?= h($task['due_date']) ?>">
                <input type="submit" class="btn submit-btn" value="更新">
            </form>

            <a href="index.php" class="btn return-btn">戻る</a>
        </div>
    </div>
</body>

</html>

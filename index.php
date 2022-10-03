<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/config.php';

/* 学習管理
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
    $errors = insert_validate($title, $due_date);

    // エラーチェック
    if (empty($errors)) {
        // タスク登録処理の実行
        insert_task($title, $due_date);
    }
}

// 未達成エリア
$notyet_tasks = find_notyet_tasks();
// 達成エリア
$done_tasks = find_done_tasks();
?>

<!DOCTYPE html>
<html lang="ja">

<!-- _head.phpの読み込み -->
<?php include_once __DIR__ . '/_head.html' ?>

<body>
    <div class="wrapper">
        <h1 class="title">学習管理アプリ</h1>
        <div class="form-area">
            <!-- エラー表示 -->
            <?php if ($errors) echo (create_err_msg($errors)) ?>

            <form action="" method="post">
                <label for="title">学習内容</label>
                <input type="text" name="title">
                <label for="due_date">期限日</label>
                <input type="date" name="due_date">
                <input type="submit" class="btn submit-btn" value="追加">
            </form>
        </div>
        <div class="incomplete-area">
            <h2 class="sub-title">未達成</h2>
            <!-- ここから未達成のテーブルエリア-->
            <table class="plan-list">
                <thead>
                    <tr>
                        <th class="plan-title">学習内容</th>
                        <th class="plan-due-date">完了期限</th>
                        <th class="done-link-area"></th>
                        <th class="edit-link-area"></th>
                        <th class="delete-link-area"></th>
                    </tr>
                </thead>
                <tbody>

                    <!-- 未完了のデータを表示 -->
                    <?php foreach ($notyet_tasks as $task) : ?>
                        <tr>
                            <td><?= h($task['title']) ?></td>
                            <?php if (date('Y-m-d') >= $task['due_date']) : ?>
                                <td class="expired"><?= h(date('Y/m/d', strtotime($task['due_date']))) ?></td>
                            <?php else : ?>
                                <td><?= h(date('Y/m/d', strtotime($task['due_date']))) ?></td>
                            <?php endif; ?>
                            <td><a href="done.php?id=<?= h($task['id']) ?>" class="btn done-btn">完了</a></td>
                            <td><a href="edit.php?id=<?= h($task['id']) ?>" class="btn edit-btn">編集</a></td>
                            <td><a href="delete.php?id=<?= h($task['id']) ?>" class="btn delete-btn">削除</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="complete-area">
            <h2 class="sub-title">完了</h2>
            <!-- ここから完了のテーブルエリア入る-->
            <table class="plan-list">
                <thead>
                    <tr>
                        <th class="plan-title">学習内容</th>
                        <th class="plan-completion-date">完了日</th>
                        <th class="done-link-area"></th>
                        <th class="edit-link-area"></th>
                        <th class="delete-link-area"></th>
                    </tr>
                </thead>
                <tbody>

                    <!-- 完了済のデータを表示 -->
                    <?php foreach ($done_tasks as $task) : ?>
                        <tr>
                            <td><?= h($task['title']) ?></td>
                            <td><?= h(date('Y/m/d', strtotime($task['completion_date']))) ?></td>
                            <td><a href="done_cancel.php?id=<?= h($task['id']) ?>" class="btn comp-btn">未完了</a></td>
                            <td><a href="edit.php?id=<?= h($task['id']) ?>" class="btn edit-btn">編集</a></td>
                            <td><a href="delete.php?id=<?= h($task['id']) ?>" class="btn delete-btn">削除</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

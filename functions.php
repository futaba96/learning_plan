<?php
require_once __DIR__ . '/config.php';
// 接続処理を行う関数
function connect_db()
{
    try {
        return new PDO(
            DSN,
            USER,
            PASSWORD,
            [PDO::ATTR_ERRMODE =>
            PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}
// エスケープ処理を行う関数
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}


// タスク登録時のバリテーション
function insert_validate($title, $due_date)
{
    // 初期化
    $errors = [];

    if (empty($title)) {
        $errors[] = MSG_TITLE_REQUIRED;
    }

    if (empty($due_date)) {
        $errors[] = MSG_DUE_DATE_REQUIRED;
    }

    return $errors;
}


// タスク登録(insert_task 関数の作成)
function insert_task($title, $due_date)
{
    // データベースに接続
    $dbh = connect_db();
    // レコードを追加
    $sql = <<<EOM
    INSERT INTO
        plans
        (
        title,
        due_date
        )
    VALUES
        (
        :title,
        :due_date
        )
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam('due_date', $due_date, PDO::PARAM_STR);
    $stmt->execute();
}


// エラーメッセージ作成
function create_err_msg($errors)
{
    $err_msg = "<ul class=\"errors\">\n";
    foreach ($errors as $error) {
        $err_msg .= "<li>" . h($error) . "<li>\n";
    }
    $err_msg .= "</ul>\n";
    return $err_msg;
}


// 更新機能
// 受け取った idレコードを取得
function find_by_id($id)
{
    $dbh = connect_db();
    $sql = <<<EOM
    SELECT
        *
    FROM
        plans
    WHERE
        id = :id
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    // 結果の取得
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// タスク更新(学習管理の編集画面)
function update_task($id, $title, $due_date)
{
    // データベースに接続
    $dbh = connect_db();
    // $id を使用してデータを更新
    $sql = <<<EOM
    UPDATE
        plans
    SET
        title = :title,
        due_date = :due_date
    WHERE
        id = :id
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam('due_date', $due_date, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}


// 学習内容の未完了処理(完了エリアにいるときに、、未完了ボタンを押せるようにする)
function update_by_notyet($id)
{
    $dbh = connect_db();
    $sql = <<<EOM
    UPDATE
        plans
    SET
        completion_date = NULL
    WHERE
        id = :id
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}


// 学習内容の完了処理(未達成エリアにいるとき、完了ボタンを押せるようにする)  
function update_by_complete($id)
{
    $dbh = connect_db();
    $sql = <<<EOM
    UPDATE
        plans
    SET
        completion_date = CURRENT_TIME
    WHERE
        id = :id
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}


//バリテーション(学習内容、期限日)
//同じ登録データがないか確認する
function validate_tasks($title, $due_date, $task)
{
    $errors = [];

    if ($title == '') {
        $errors[] = MSG_TITLE_REQUIRED;
    }
    if ($due_date == '') {
        $errors[] = MSG_DUE_DATE_REQUIRED;
    }

    if ($title == $task['title'] && $due_date == $task['due_date']) {
        $errors[] = MSG_TASK_NO_CHANGE;
    }
    return $errors;
}


// 学習内容の削除
function delete_tasks($id)
{
    $dbh = connect_db();
    $sql = <<<EOM
    DELETE FROM
        plans
    WHERE
        id = :id
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}


// 未達成エリアの受け取り
function find_notyet_tasks()
{
    $dbh = connect_db();
    $sql = <<<EOM
    SELECT
        *
    FROM
        plans
    WHERE
        completion_date IS NULL
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    // 結果の表示
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 完了エリアの受け取り
function find_done_tasks()
{
    $dbh = connect_db();
    $sql = <<<EOM
    SELECT
        *
    FROM
        plans
    WHERE
        completion_date IS NOT NULL
    ORDER BY
        completion_date DESC
    EOM;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

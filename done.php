<?php

require_once __DIR__ . '/functions.php';

// index.php から渡された id を受け取る
$id = filter_input(INPUT_GET, 'id');

// タスク完了処理の実行
//update_done_by_id関数を呼び出す処理
update_by_complete($id);

// index.php にリダイレクト
header('Location: index.php');
exit;

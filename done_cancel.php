<?php
require_once __DIR__ . '/functions.php';

// index.php から渡された id を受け取る
$id = filter_input(INPUT_GET, 'id');

//タスク未完了処理の実行
update_by_notyet($id);

header('Location: index.php');
exit;

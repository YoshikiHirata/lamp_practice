<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

//全ての商品データ取得
$items = get_open_items($db);
//総商品件数
$total_items = count($items);
//総ページ数 ※ceilは小数点を切り捨てる関数
$total_page = ceil($total_items / ITEMS_PER_PAGE);

//ページ数の取得 現在のページの値がなかったら1ページ目が表示される
if(!isset($_GET['page'])){
  $now_page = 1;
}else{
  $now_page = (int)$_GET['page'];
}

//表示始めの配列の取得
$start_items = ($now_page - 1) * ITEMS_PER_PAGE;
//$itemsという配列の$start_items番目からITEM_PEA_PAGE個の配列を取得
$out_items = array_slice($items,$start_items,ITEMS_PER_PAGE);

//件数表示の為の変数
$beginning_item = $start_items + 1;
$last_item = min(($now_page * ITEMS_PER_PAGE), $total_items);
include_once VIEW_PATH . 'index_view.php';
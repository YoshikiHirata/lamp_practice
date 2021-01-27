<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'order_detail.php';

session_start();

//ログイン中でない場合ログインページへリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$token = get_csrf_token();
$order_id = get_post('order_id');
//is_admin関数でログイン中ユーザーが管理者かどうか確認
if(is_admin($user) === true){
  $details = get_admin_details($db, $order_id);
  $orders = get_user_orders($db, null, $order_id);
} else {
  //管理者でない場合user_idを引数に入れる
  $details = get_user_details($db, $order_id, $user['user_id']);
  $orders = get_user_orders($db, $user['user_id'], $order_id);
}

//orders_view.phpに飛ぶ
include_once VIEW_PATH . 'detail_view.php';
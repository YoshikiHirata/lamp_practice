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

//is_admin関数でログイン中ユーザーが管理者かどうか確認
if(is_admin($user) === true){
  $orders = get_user_orders($db);
} else {
  //管理者でない場合user_idを引数に入れる
  $orders = get_user_orders($db, $user['user_id']);
}
$order_id = get_post('order_id');

//orders_view.phpに飛ぶ
include_once VIEW_PATH . 'order_view.php';
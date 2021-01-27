<?php 
require_once MODEL_PATH . 'db.php';

//ユーザー購入履歴の取得(配列)
//引数の'$user_id=null'　= 管理者という状態
function get_user_orders($db, $user_id=null, $order_id=null){
  //空の配列を用意
  $in_user = [];
  //sql文で購入履歴情報を取得
  $sql = "
    SELECT
      orders.order_id,
      created,
      sum(price * amount) as total
    FROM
      orders
    JOIN
      details
    ON
      orders.order_id = details.order_id
    ";
    //user_idがnullでない場合、つまり管理者でない場合。管理者の場合はnullが入るためそのまま下のsql文へ飛ぶ
    if($user_id !== null){
      $sql.= " WHERE user_id = ? ";
      $in_user[] = $user_id;
    }
    if($order_id !== null){
      if(count($in_user) === 0){
        $sql.= " WHERE details.order_id = ? ";
      } else {
        $sql.= " AND details.order_id = ? ";
      }
      $in_user[] = $order_id;
    }
    $sql.= " GROUP BY
      orders.order_id 
      order by 
      created DESC";
  return fetch_all_query($db, $sql, $in_user);
}

//ユーザー購入明細の取得
function get_user_details($db, $order_id, $user_id){
  $sql = "
    SELECT
      details.price,
      amount,
      details.price * amount as subtotal,
      name
    FROM
      details
    JOIN
      items
    ON
      details.item_id = items.item_id
    WHERE
      order_id = ?
    AND 
    EXISTS(SELECT * FROM orders WHERE order_id = ? AND user_id = ?)
  ";
  return fetch_all_query($db, $sql, [$order_id, $order_id, $user_id]);
}

//管理者の購入明細情報取得
function get_admin_details($db, $order_id){
  $sql = "
    SELECT 
      details.price, 
      amount, 
      details.price * amount AS subtotal, 
      name
    FROM 
      details
    JOIN 
      items ON details.item_id = items.item_id
    WHERE 
      order_id = ?
  ";
  return fetch_all_query($db, $sql, [$order_id]);
}
<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//ユーザーカート情報の取得(配列)
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, [$user_id]);
}

//ユーザーカート情報の取得(単一の行)
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, [$user_id,$item_id]);

}

//カートテーブルへの追加処理
function add_cart($db, $user_id, $item_id ) {
  //$cartに取得した値を代入
  $cart = get_user_cart($db, $user_id, $item_id);
  //新規の場合
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  //既存の場合
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//カートテーブルにデータ(値)を入れる
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, [$item_id,$user_id,$amount]);
}

//既存のカート情報をアップデート
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, [$amount, $cart_id]);
}

//カート情報の削除処理
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, [$cart_id]);
}

//カート内購入処理
function purchase_carts($db, $carts){
  //購入処理で何かしらのエラーがある場合 false
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  foreach($carts as $cart){
    //在庫数より購入個数が多い場合
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  //カート内情報の消去
  delete_user_carts($db, $carts[0]['user_id']);
}

//カート内情報の消去処理
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, [$user_id]);
}

//カート内の商品合計金額
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

//カート内商品の購入処理エラーチェック関数 validate(検証) purchase(購入)
function validate_cart_purchase($carts){
  //商品が入ってなければfalse
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    //商品が非公開の場合
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    //商品の在庫不足の場合
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  //エラーがある場合
  if(has_error() === true){
    return false;
  }
  //何もなければpurchase_cartsにtrueを返す
  return true;
}

//order, detailsテーブルへの追加処理
function add_order_details($db, $user_id, $item_id ) {
  //$cartに取得した値を代入
  $cart = get_user_cart($db, $user_id, $item_id);
  //orderテーブルへ追加する場合
  if($cart['user_id'] === false){
    return insert_order($db, $cart['user_id']);
  }
  //detailsテーブルに追加する場合
  return insert_details($db, $order_id, $cart['item_id'], $cart['price'], $cart['amount'] );
}

//orderテーブルにデータ(値)を入れる
function insert_order($db, $user_id){
  $sql = "
    INSERT INTO
      order(
        user_id,
      )
    VALUES(?)
  ";
  return execute_query($db, $sql, [$user_id]);
}

//detailsテーブルにデータ(値)を入れる
function insert_details($db, $order_id, $item_id, $price, $amount){
  $sql = "
    INSERT INTO
      details(
        order_id
        item_id,
        price,
        amount
      )
    VALUES(?, ?, ?, ?)
  ";

  return execute_query($db, $sql, [$order_id, $item_id, $price, $amount]);
}

<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>購入商品金額</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($orders as $order){ ?>
          <tr>
            <td><?php print h($order['order_id']); ?></td>
            <td><?php print h($order['created']); ?></td>
            <td><?php print number_format(h($order['total'])); ?>円</td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($details as $detail){ ?>
          <tr>
            <td><?php print(h($detail['name'])); ?></td>
            <td><?php print(number_format(h($detail['price']))); ?>円</td>
            <td><?php print(h($detail['amount'])); ?>個</td>
            <td><?php print(number_format(h($detail['subtotal']))); ?>円</td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
  </div>
</body>
</html>
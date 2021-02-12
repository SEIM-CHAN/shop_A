<?php session_start(); ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>購入画面</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<?php require 'menu.php'; ?>
    <?php 
        require 'db_connect.php';
        // purchaseテーブル最終行　id+1を取得
        $purchase_id = 1;
        foreach ($pdo->query('select max(id) from purchase') as $row) {
            $purchase_id = $row['max(id)'] + 1;
        }
        //SQL文を作る（プレースホルダを作った式）
        $sql = "INSERT INTO purchase values(:id, :customer_id)";
        //プリペアードステートメントを作る
        $stm = $pdo->prepare($sql);
        //プリペアーステートメントに値をバインドする
        $stm->bindValue(':id', $purchase_id, PDO::PARAM_INT);
        $stm->bindValue(':customer_id', $_SESSION['cunstomer']['id'], PDO::PARAM_INT);
        if($stm->execute()){
            echo "a";
            //SQL成功
            //セッションに入っている商品の数だけpurchase_detalに保存
            foreach($_SESSION['product'] as $product_id => $product) {
                // SQl文を作る(プレースホルダを使った式)
                $sql = "INSERT INTO purchase_detail VALUES(:purchase_id, :product_id, :count)";
                //プリペアードステートメントを作る
                $stm = $pdo->prepare($sql);
                //プリペアードステートメントに値をバインドする
                $stm->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
                $stm->bindValue(':product_id', $product_id, PDO::PARAM_INT);
                $stm->bindValue(':count', $product['count'], PDO::PARAM_INT);
                //SQL文を実行する
                $stm->execute();
            }
            unset($_SESSION['product']);
            echo "購入手続きが完了しました。ありがとうございます。";
        }else {
            //SQL失敗
            echo '購入手続き中にエラーが発生しました。申し訳ございません。';
        }
    ?>
</body>
</html>

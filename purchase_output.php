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
		//MySQLデータベースに接続する
		require 'db_connect.php';
		//purchaseテーブル最終行 id + 1 を取得
		$purchase_id = 1;
		foreach ($pdo->query('select max(id) from purchase') as $row) {
			$purchase_id = $row['max(id)'] + 1;
		}
		//SQL文を作成する
		$sql = "INSERT INTO purchase VALUES(:id, :customer_id)";
		//プリペアードステートメントを作成
		$stm = $pdo->prepare($sql);
		$stm->bindValue(':id', $purchase_id, PDO::PARAM_INT);  //実際には入力されたデータをエスケープやtrimする
		$stm->bindValue(':customer_id', $_SESSION['customer']['id'], PDO::PARAM_INT);  //実際には入力されたデータをエスケープやtrimする
		if ($stm->execute()) {
			//SQL成功
			//セッションに入ってる商品の数だけpurchase_detailに保存
			foreach ($_SESSION['product'] as $product_id => $product) {
				$sql = "INSERT INTO purchase_detail VALUES(:purchase_id, :product_id, :count)";
				//プリペアードステートメントを作成
				$stm = $pdo->prepare($sql);
				//プリペアードステートメントに値をバインド
				$stm->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
				$stm->bindValue(':product_id', $product_id, PDO::PARAM_INT);
				$stm->bindValue(':count', $product['count'], PDO::PARAM_INT);
				//SQL文を実行
				$stm->execute();
			}
			unset($_SESSION['product']);
			echo '購入手続きが完了しました。ありがとうございます。';
		} else {
			//SQL失敗
			echo "購入手続き中にエラーが発生しました。申し訳ございません。";
		}
	?>

</body>
 
</html>
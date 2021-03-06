<?php
    require_once 'google/appengine/api/users/UserService.php';  

	// ============================User service==================================
    use google\appengine\api\users\User;
    use google\appengine\api\users\UserService;

    $user = UserService::getCurrentUser();  // 取得目前已經登入的使用者

    if ($user===null) {
        header('Location: ' .                                       // 如果未登入，將使用者導向登入頁面，
             UserService::createLoginURL($_SERVER['REQUEST_URI']));	// 當登入成功後再導回來現在這個頁面($_SERVER['REQUEST_URI'])	
		exit;
    }  
?>

<html>
<head>	
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="/stylesheets/main.css" />
</head>
<body>

<?php
    
    echo 'Hello, [' . htmlspecialchars($user->getNickname()) ."] ";		// 顯示使用者的暱稱
    echo 'Not you? <a href=' . UserService::createLogoutURL($_SERVER['REQUEST_URI']).">Sing Out</a>"."<BR>";	
	// ============================User service==================================
	
	
	// ============================Cloud SQL==================================
	// Development DB
	$db = new PDO('mysql:host=localhost;dbname=guestbook','root','');
	// Production DB
	//$db = new PDO('mysql:unix_socket=/cloudsql/regal-center-453:guestbook;dbname=guestbook;charset=utf8','root',  '');
	// ============================Cloud SQL==================================

	// 一個簡單的form, 儲存POST過來的資料
    if (array_key_exists('content', $_POST)) {
		$sql = "INSERT INTO  `guestbook`.`message` (`message` ,`user`)
				VALUES ( '".htmlspecialchars($_POST['content'])."',  '".htmlspecialchars($user->getNickname())."')";
		$count = $db->exec($sql );
		echo "成功新增 $count 筆留言";
    }
?>
    <form action="/sign" method="post">
      <div><textarea name="content" rows="3" cols="60"></textarea></div>
      <div><input type="submit" value="Sign Guestbook"></div>
    </form>
	<table border=1 width=500>
		<tr><td>編號</td><td>留言</td><td>訪客名稱</td></tr>
<?php


foreach($db->query('select * from message') as $row) {
  echo "<tr><td>".$row['id']."</td><td>".$row['message']."</td><td>".$row['user']."</td></tr>";
}

	
	
?>
	</table>
	
</body>
</html>

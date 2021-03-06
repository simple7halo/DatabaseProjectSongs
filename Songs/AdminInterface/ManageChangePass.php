<?php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   
   require_once '../database/database.php';

   session_start();
   setLanguage();

   $LoginName = $_SESSION['login_user'];

   $pdo=db_connect();

   change_password("admin");

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo DISPLAY['admin_interface']; ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="../Script/KeyBoard.js" charset="UTF-8"></script>
    <link rel="stylesheet" href="../style.css">
</head>    
<body>
    <h1>
        <?php echo DISPLAY['welcome_message'].$LoginName;; ?>
    </h1>
    <nav class="topNav">
        <ul>
            <li><a href="ManageChangePass.php"><?php echo DISPLAY['change_pass']; ?></a></li>
            <li><a href="ManageSearch.php"><?php echo DISPLAY['search']; ?></a></li>
            <li><a href="addsong.php"><?php echo DISPLAY['add_song']; ?></a></li>
        </ul>
    </nav>
        
    <div style="text-align: center;">
      <h3><?php echo DISPLAY['change_pass']; ?></h3>
      <form action = "" method = "post">
        <label><?php echo DISPLAY['old_pass']; ?> :  </label><input type = "password" name = "old" class = "box">
        <br/><br/>
        <label><?php echo DISPLAY['new_pass']; ?> :  </label><input type = "password" name = "new" class = "box">
        <br/><br/>
        <input type = "submit" value = " Submit ">
        <br/>
      </form>
    </div> 

    <img src="../Img/Icon.png" class="icon" alt="animated music icon">
    <h2 class="logout"><a href = "../logout.php"><?php echo DISPLAY['signout']; ?></a></h2>
    <div class="language">
        <h3><?php echo DISPLAY['display']; ?></h3>
	    <ul>
            <li><a href="?lan=English">English</a></li>
            <li><a href="?lan=Chinese">中文</a></li>
		</ul>
    </div>
          
</body>
</html>
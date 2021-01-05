<?php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   
   require_once '../database/database.php';

   session_start();
   setLanguage();

   $pdo=db_connect();
   validate_user_login("admin");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo DISPLAY['ManageLogin']; ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="../Script/KeyBoard.js" charset="UTF-8"></script>
    <link rel="stylesheet" href="../style.css">
</head>    
<body>
   <h1>
      <?php echo DISPLAY['reg_title']; ?>  
   </h1>
   <nav class="topNav">
      <ul>
         <li><a href="../Index.php"><?php echo DISPLAY['home']; ?></a></li>
         <li><a href="../Search.php"><?php echo DISPLAY['search']; ?></a></li>
         <li><a href="../Registration.php"><?php echo DISPLAY['login']; ?></a></li>
      </ul>
   </nav>
        
   <div class="loginform">
      <h3><?php echo DISPLAY['ManageLogin']; ?></h3>
      <form action = "" method = "post">
         <label><?php echo DISPLAY['username']; ?> :  </label><input type = "text" name = "username" class = "box">
         <br/><br/>
         <label><?php echo DISPLAY['password']; ?> :  </label><input type = "password" name = "password" class = "box">
         <br/><br/>
         <input type = "submit" value = " Submit ">
         <br/>
      </form>
   </div>
        

   <img src="../Img/Icon.png" class="icon" alt="animated music icon">
   <div class="language">
      <h3><?php echo DISPLAY['display']; ?></h3>
		<ul>
         <li><a href="?lan=English">English</a></li>
         <li><a href="?lan=Chinese">中文</a></li>
		</ul>
   </div>
</body>
</html>
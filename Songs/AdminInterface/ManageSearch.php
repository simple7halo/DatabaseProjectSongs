<?php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   
   require_once '../database/database.php';

   session_start();
   setLanguage();

   $pdo=db_connect();
   $LoginName = $_SESSION['login_user'];

   search_song("admin");

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo DISPLAY['search_title']; ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="../Script/KeyBoard.js" charset="UTF-8"></script>
    <link rel="stylesheet" href="../style.css">
</head>    
<body>
    <h1>
        <?php echo DISPLAY['welcome_message_search'].$LoginName;; ?>
    </h1>
    <nav class="topNav">
        <ul>
            <li><a href="ManageChangePass.php"><?php echo DISPLAY['change_pass']; ?></a></li>
            <li><a href="ManageSearch.php"><?php echo DISPLAY['search']; ?></a></li>
            <li><a href="addsong.php"><?php echo DISPLAY['add_song']; ?></a></li>
        </ul>
    </nav>
        
    <div class="loginform">
        <h3><?php echo DISPLAY['search_title']; ?></h3>
        <form action = "" method = "post">
            <label><?php echo DISPLAY['search_by']; ?> :  </label>
            <select name = "searchby" class = "box">
                <option value="Name"><?php echo DISPLAY['song_name']; ?></option>
                <option value="Singer"><?php echo DISPLAY['singer']; ?></option>
                <option value="Writer"><?php echo DISPLAY['writer']; ?></option>
                <option value="Album"><?php echo DISPLAY['album']; ?></option>
                <option value="Tag"><?php echo DISPLAY['tag']; ?></option>
            </select>
            <br/><br/>
            <label><?php echo DISPLAY['keyword']; ?> :  </label><input type = "text" name = "keyword" class = "box">
            <br/><br/>
            <input type = "submit" value = <?php echo DISPLAY['submit']; ?>>
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
<?php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   
   require_once '../database/database.php';

   session_start();
   setLanguage();

   $pdo=db_connect();
   $LoginName = $_SESSION['login_user'];
   $count = $_SESSION["result_count"];
   $result = $_SESSION["result"];

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
        
        <?php
        echo "<h3>".$count." songs found!</h3>";
        echo
        "<table style='width:100%'>
            <tr>
                <th>".DISPLAY['song_name']."</th>
                <th>".DISPLAY['singer']."</th> 
                <th>".DISPLAY['writer']."</th>
                <th>".DISPLAY['album']."</th>
                <th>".DISPLAY['tag']."</th>
            </tr>";
        
        foreach ($result as $rows) {
            $row = $rows['code'];
          
            $sql = "SELECT Name From songbelongalbum WHERE Code = :code ";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':code', $row);
            $statement->execute();
            $name = $statement->fetch();

            $sql = 
            "SELECT musicpeople.Name 
            From singersong, musicpeople
            WHERE singersong.SongCode = :code 
            AND singersong.PeopleCode = musicpeople.Code";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':code', $row);
            $statement->execute();
            $singer = $statement->fetch();

            $sql = 
            "SELECT musicpeople.Name 
            From writtersong, musicpeople
            WHERE writtersong.SongCode = :code 
            AND writtersong.PeopleCode = musicpeople.Code";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':code', $row);
            $statement->execute();
            $writer = $statement->fetch();

            $sql = 
            "SELECT album.Name 
            From songbelongalbum, album
            WHERE songbelongalbum.Code = :code 
            AND songbelongalbum.AlbumCode = album.Code";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':code', $row);
            $statement->execute();
            $album = $statement->fetch();

            $sql = 
            "SELECT Tag 
            From songwithtag
            WHERE songwithtag.SongCode = :code ";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':code', $row);
            $statement->execute();
            $tag = $statement->fetchAll(PDO::FETCH_ASSOC);
            $tag_out = "";

            if ($statement->rowCount() > 0){
                foreach ($tag as $tag_row){
                    $tag_out .= $tag_row["Tag"]." ";
                }
            }
            echo 
            "<tr><td>".$name[0].
            "</td><td>".$singer[0].
            "</td><td>".$writer[0].
            "</td><td>".$album[0].
            "</td><td>".$tag_out."</br><a href='addtag.php?id=".$row."'>".DISPLAY['add_tag']."</a>".
            "</td></tr>";
          }
          echo "</table>";
        
        
        ?>

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
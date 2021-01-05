<?php
require 'config.php';

// Should return a PDO
function db_connect() {

  try {
    // try to open database connection using constants set in config.php
    // return $pdo;
    $connectionString = 'mysql:host='.DBHOST.';dbname='.DBNAME;
    $pdo = new PDO($connectionString, DBUSER, DBPASS);
    return $pdo;
  }

  catch (PDOException $e)
  {
    die($e->getMessage());
  }

}

//set the display language of the page
function setLanguage() {
  if (isset($_GET['lan'])){
    switch ($_GET['lan']){
      case 'English': 
        require_once ROOTPATH."/Language/English.php";
        break;
      case 'Chinese':
        require_once ROOTPATH."/Language/Chinese.php";
        break;
      default:
        require_once ROOTPATH."/Language/English.php";
        break;
    }
    $_SESSION['lan'] = $_GET['lan'];
  }else{
    if (isset($_SESSION['lan'])){
      switch ($_SESSION['lan']){
        case 'English': 
          require_once ROOTPATH."/Language/English.php";
          break;
        case 'Chinese':
          require_once ROOTPATH."/Language/Chinese.php";
          break;
        default:
          require_once ROOTPATH."/Language/English.php";
          break;
      }
    }else
      require_once ROOTPATH."/Language/English.php";
  }
  unset($_GET['lan']);
}

// Validate user login info
function validate_user_login($user) {
  global $pdo;

  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    try{
      if ($user == "user")
        $sql = "SELECT * FROM user WHERE ID = :username AND Password = :pass";
      else if ($user == "admin")
        $sql = "SELECT * FROM admin WHERE ID = :username AND Password = :pass";
      $statement = $pdo->prepare($sql);
      $statement->bindValue(':username', $_POST['username']);
      $statement->bindValue(':pass', $_POST['password']);
      $statement->execute();
      $count = $statement->rowCount();

      if($count == 1) {
        $_SESSION['login_user'] = $_POST['username'];
        
        if ($user == "user")
          header("location: RegularLoggedIn.php");
        else if ($user == "admin")
          header("location: ManageLoggedIn.php");
      }else {
        $message = DISPLAY['login_fail_alert'];
        echo "<script>alert('$message');</script>";
      }

    }catch (PDOException $e){
      die($e->getMessage());
    }
  }
}

// Change Password
function change_password($user) {
  global $pdo;

  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    try{
      if ($user == "user")
        $sql = "SELECT Password FROM user WHERE ID = :username AND Password = :oldpass ";
      else if ($user == "admin")
        $sql = "SELECT Password FROM admin WHERE ID = :username AND Password = :oldpass ";
      $statement = $pdo->prepare($sql);
      $statement->bindValue(':username', $_SESSION['login_user']);
      $statement->bindValue(':oldpass', $_POST['old']);
      $statement->execute();
      $count = $statement->rowCount();

      if($count == 1) {
        if ($user == "user")
          $newPass_sql = 
          "UPDATE user 
          SET Password = :newpass
          WHERE ID = :username ";
        else if ($user == "admin")
          $newPass_sql = 
          "UPDATE admin 
          SET Password = :newpass
          WHERE ID = :username ";

        $statement = $pdo->prepare($newPass_sql);
        $statement->bindValue(':username', $_SESSION['login_user']);
        $statement->bindValue(':newpass', $_POST['new']);
        $statement->execute();

        $message = DISPLAY['pass_updated'];
        echo "<script>alert('$message');</script>";
     }else {
      $message = DISPLAY['incorrect_old_pass'];
      echo "<script>alert('$message');</script>";
     }

    }catch (PDOException $e){
      die($e->getMessage());
    }
  }
}

//search song
function search_song($user) {
  global $pdo;

  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    try{
      switch ($_POST["searchby"]){
        case "Name":
          $sql = 
          "SELECT songbelongalbum.Code AS code
          FROM songbelongalbum
          WHERE songbelongalbum.Name = :keyword";
          break;
        case "Singer":
          $sql = 
          "SELECT songbelongalbum.Code AS code
          FROM songbelongalbum, singersong, musicpeople
          WHERE musicpeople.Name = :keyword
          AND songbelongalbum.Code = singersong.SongCode
          AND singersong.PeopleCode = musicpeople.Code";
          break;
        case "Writer":
          $sql = 
          "SELECT songbelongalbum.Code AS code
          FROM songbelongalbum, writtersong, musicpeople
          WHERE musicpeople.Name = :keyword
          AND songbelongalbum.Code = writtersong.SongCode
          AND writtersong.PeopleCode = musicpeople.Code";
          break;
        case "Album":
          $sql = 
          "SELECT songbelongalbum.Code AS code
          FROM songbelongalbum, album
          WHERE album.Name = :keyword
          AND songbelongalbum.AlbumCode = album.Code";
          break;
        case "Tag":
          $sql = 
          "SELECT songbelongalbum.Code AS code
          FROM songbelongalbum, songwithtag
          WHERE songwithtag.Tag = :keyword
          AND songbelongalbum.AlbumCode = songwithtag.SongCode";
          break;
      }
      
      $statement = $pdo->prepare($sql);
      $statement->bindValue(':keyword', $_POST['keyword']);
      $statement->execute();
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);
      $count = $statement->rowCount();
      
      if($count > 0) {
        $_SESSION["result_count"] = $count;
        $_SESSION["result"] = $result;

        if ($user == "null")
          header("Location: Display.php");
        else if ($user =="admin")
          header("Location: ManageDisplay.php");
        else if ($user =="user")
          header("Location: UserDisplay.php");

      }else {
        $message = DISPLAY['no_result'];
        echo "<script>alert('$message');</script>";
      }

    }catch (PDOException $e){
      die($e->getMessage());
    }
  }
}

//add tag
function add_tag($user) {
  global $pdo;

  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    try{

      if (isset($_GET['id'])){
        $_SESSION['song_id'] = $_GET['id'];
        $id = $_GET['id'];
      }else if (isset($_SESSION['song_id'])){
        $id = $_SESSION['song_id'];
      }

      $tag = $_POST['keyword'];
        $sql = 
        "SELECT *
        FROM songwithtag
        WHERE SongCode = :code
        AND Tag = :Tag";
          
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':code', $id, PDO::PARAM_INT);
      $statement->bindValue(':Tag', $tag);
      $statement->execute();
      $count = $statement->rowCount();
      
      if($count == 0) {
        $sql = 
        "INSERT INTO songwithtag(SongCode, Tag)
        VALUES(:code, :Tag)";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':code', $id, PDO::PARAM_INT);
        $statement->bindValue(':Tag', $tag);
        $statement->execute();

        $message = DISPLAY['add_tag_succ'];
        echo "<script>alert('$message');</script>";
      }else {
        $message = DISPLAY['duplicate_tag'];
        echo "<script>alert('$message');</script>";
      }

    }catch (PDOException $e){
      die($e->getMessage());
    }
  }
}

//get song name
function get_song_name() {
  global $pdo;

    try{
      
        $sql = 
        "SELECT songbelongalbum.Name
        FROM songbelongalbum
        WHERE songbelongalbum.Code = :keyword";
      
      if (isset($_GET['id'])){
        $_SESSION['song_id'] = $_GET['id'];
        $id = $_GET['id'];
      }else if (isset($_SESSION['song_id'])){
        $id = $_SESSION['song_id'];
      }
      
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':keyword', $id, PDO::PARAM_INT);
      $statement->execute();
      $result = $statement->fetch(PDO::FETCH_ASSOC);
  
      return $result['Name'];

    }catch (PDOException $e){
      die($e->getMessage());
    }
  
}

//get option
function get_option() {
  global $pdo;

    try{
      //album
      $sql = 
      "SELECT Code
      FROM album";
      
      $statement = $pdo->prepare($sql);
      $statement->execute();
      $album = $statement->fetchAll(PDO::FETCH_ASSOC);
  
      $sql = 
      "SELECT Name
      FROM album
      WHERE Code = :id";
      $statement = $pdo->prepare($sql);

      echo "<label>".DISPLAY['album']." :  </label><select name= 'album' class = 'box'>";
      foreach($album AS $album_code){
        $statement->bindParam(':id', $album_code['Code'], PDO::PARAM_INT);
        $statement->execute();
        $name = $statement->fetch();
        echo "<option value='".$album_code['Code']."'>".$name['Name']."</option>";
      }
      echo "</select>";

      //singer
      $sql = 
      "SELECT Code
      FROM musicpeople";
      
      $statement = $pdo->prepare($sql);
      $statement->execute();
      $singer = $statement->fetchAll(PDO::FETCH_ASSOC);
  
      $sql = 
      "SELECT Name
      FROM musicpeople
      WHERE Code = :id";
      $statement = $pdo->prepare($sql);

      echo "<label>".DISPLAY['singer']." :  </label><select name= 'singer' class = 'box'>";
      foreach($singer AS $singer_code){
        $statement->bindParam(':id', $singer_code['Code'], PDO::PARAM_INT);
        $statement->execute();
        $name = $statement->fetch();
        echo "<option value='".$singer_code['Code']."'>".$name['Name']."</option>";
      }
      echo "</select>";

      //writer
      $sql = 
      "SELECT Code
      FROM musicpeople";
      
      $statement = $pdo->prepare($sql);
      $statement->execute();
      $writer = $statement->fetchAll(PDO::FETCH_ASSOC);
  
      $sql = 
      "SELECT Name
      FROM musicpeople
      WHERE Code = :id";
      $statement = $pdo->prepare($sql);

      echo "<label>".DISPLAY['writer']." :  </label><select name= 'writer' class = 'box'>";
      foreach($writer AS $writer_code){
        $statement->bindParam(':id', $writer_code['Code'], PDO::PARAM_INT);
        $statement->execute();
        $name = $statement->fetch();
        echo "<option value='".$writer_code['Code']."'>".$name['Name']."</option>";
      }
      echo "</select>";

    }catch (PDOException $e){
      die($e->getMessage());
    }
  
}

function add_song(){

  global $pdo;

  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    try{
      $sql = 
      "INSERT INTO songbelongalbum(Name, AlbumCode)
      VALUES(:name, :albumCode)";
      
      //album
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':albumCode', $_POST['album'], PDO::PARAM_INT);
      $statement->bindValue(':name', $_POST['name']);
      $statement->execute();

      if ($statement->rowCount() == 1){
        $sql =
        "SELECT MAX(Code)
        FROM songbelongalbum";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $id = $statement->fetch(PDO::FETCH_ASSOC);

        //singer
        $sql = 
          "INSERT INTO singersong(SongCode, PeopleCode)
          VALUES(:songCode, :peoplCode)";
              
          $statement = $pdo->prepare($sql);
          $statement->bindParam(':peoplCode', $_POST['singer'], PDO::PARAM_INT);
          $statement->bindParam(':songCode', $id['MAX(Code)'], PDO::PARAM_INT);
          $statement->execute();

          if ($statement->rowCount() == 1){
            //writer
            $sql = 
              "INSERT INTO writtersong(SongCode, PeopleCode)
              VALUES(:songCode, :peoplCode)";
                  
              $statement = $pdo->prepare($sql);
              $statement->bindParam(':peoplCode', $_POST['writer'], PDO::PARAM_INT);
              $statement->bindParam(':songCode', $id['MAX(Code)'], PDO::PARAM_INT);
              $statement->execute();
              if ($statement->rowCount() == 1){
                $message = DISPLAY['add_song_succ'];
                echo "<script>alert('$message');</script>";
              }else {
                $message = DISPLAY['add_song_fail'];
                echo "<script>alert('$message');</script>";
              }
            }else {
              $message = DISPLAY['add_song_fail'];
              echo "<script>alert('$message');</script>";
            }
            }else {
              $message = DISPLAY['add_song_fail'];
              echo "<script>alert('$message');</script>";
            }

    }catch (PDOException $e){
      die($e->getMessage());
    }
  }
}




//debugging function
function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}
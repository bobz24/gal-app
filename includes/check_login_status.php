<?php  
session_start();
include_once "db_con.php";
//----There is no need to include the db_con script
//----and\or start a new session on any file
//----which includes THIS script------//

//---initialize some local vars---// <----This is working perfectly!
$user_ok = false;
$log_id = "";
$log_username = "";
$log_pass = "";

function evalLoggedUser($conx, $id, $u, $p){
    $sql = "SELECT email FROM members WHERE id = '$id' AND user = '$u' AND pass = '$p' AND activated='1' LIMIT 1"; //change @12:29AM 22/3/14 ~added "activated" condition and changed "AND" clause before pass to all CAPS
    $query = mysqli_query($conx, $sql);
    $rows = mysqli_num_rows($query);
    
    if($rows > 0){
        return true;
    }
}

if(isset($_SESSION['userid']) && isset($_SESSION['username']) && isset($_SESSION['password'])){
    $log_id = preg_replace('#[^0-9]#', '', $_SESSION['userid']);
    $log_username = preg_replace('#[^a-z0-9]#i', '', $_SESSION['username']);
    $log_pass = $_SESSION['password'];
    //--verify user -- if verified set user_ok to TRUE--//
    $user_ok = evalLoggedUser($db_con, $log_id, $log_username, $log_pass);
}
elseif(isset($_COOKIE['id']) && isset($_COOKIE['user']) && isset($_COOKIE['pass'])){
    $_SESSION['userid'] = preg_replace('#[^0-9]#', '', $_COOKIE['id']);
    $_SESSION['username'] = preg_replace('#[^a-z0-9]#i', '', $_COOKIE['user']);
    $_SESSION['password'] = preg_replace('#[^a-z0-9]#i', '', $_COOKIE['pass']);
    $log_id = $_SESSION['userid'];
    $log_username = $_SESSION['username'];
    $log_pass = $_SESSION['password'];
     //--verify user -- if verified set user_ok to TRUE--//
    $user_ok = evalLoggedUser($db_con, $log_id, $log_username, $log_pass);
    
    if($user_ok == true){
        //--UPDATE lastlogin DATETIME field in db----//
        $sql = "UPDATE members SET lastlogin = now() WHERE id = '$log_id' LIMIT 1";
        $query = mysqli_query($db_con, $sql);
    }
    
}
?>
<?php
session_start();
// Set Session data to an empty array
$_SESSION = array();
// Expire their cookie files
if(isset($_COOKIE["id"]) && isset($_COOKIE["user"]) && isset($_COOKIE["pass"])) {
    setcookie("id", '', strtotime( '-5 days' ), '/');
    setcookie("user", '', strtotime( '-5 days' ), '/');
    setcookie("pass", '', strtotime( '-5 days' ), '/');
}
// Destroy the session variables
session_destroy();
// Double check to see if their sessions exists
if(isset($_SESSION['username'])){
    echo "Oops! You broke the website :\ <a href='index.php'>click here</a> to go back.";
}
else {
    header("location: index.php");
    exit();
} 


/* ----OLD_LOGOUT SCRIPT---- session_start();
$_SESSION = array();
    
    if(session_id() != "" || isset($_COOKIE[session_name()]))
    {
        setcookie(session_name(), '', time()-2592000, '/');
    }
    session_destroy();
    
//-------DOUBLECHECK TO SEE IF SESSION DESTROYED----//
if(isset($_SESSION['username'])){
    echo "Oops! You broke the website :\ <a href='index.php'>click here</a> to go back.";
}
else{
    header("//location: index.php");
    //exit();
//}
?>
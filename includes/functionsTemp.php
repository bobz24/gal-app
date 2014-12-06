<?php //functions.php
$dbhost = 'localhost';
$dbname = 'galdb';
$dbuser = 'root';
$dbpass = '';
$appname = "Get a Life [alpha]";

$db_con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
//mysqli_select_db($db_con, $dbname) or die();
//Evaluate DB connection
if(mysqli_connect_errno())
{
    echo "Could not connect to database...";
    echo mysqli_connect_error();
    exit();
}
else echo "Connected to database";

function createTable($name, $query)
{
    queryMysql("CREATE TABLE IF NOT EXISTS $name $query");
    echo "Table $name created or already exists.<br/>";
}

function queryMysql(/*$name,*/ $query)
{
    $result = mysqli_query($GLOBALS['db_con'],$query) or die(mysqli_connect_error());
    return $result;
}

function destroySession()
{
    $_SESSION = array();
    
    if(session_id() != "" || isset($_COOKIE[session_name()]))
    {
        setcookie(session_name(), '', time()-2592000, '/');
    }
    session_destroy();
}

function sanitizeString($var)
{
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return mysqli_real_escape_string($GLOBALS['db_con'], $var);
}

function showProfile($user)
{
    if(file_exists("$user.jpg")){
        echo "<img src = '$user.jpg' align = 'left'/>";
    }
    
    $result = queryMysql("SELECT * FROM profiles WHERE user = '$user'");
    
    if(mysqli_num_rows($result))
    {
        $row = mysqli_fetch_row($result);
        echo stripslashes($row[1]). "<br clear=left/><br/>";
    }
}
?>
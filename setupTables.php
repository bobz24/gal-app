<?php
include_once "./includes/db_con.php";

$tbl_members = "CREATE TABLE IF NOT EXISTS members(
                                    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                                    user VARCHAR(16) UNIQUE KEY,
                                    fname VARCHAR(16),
                                    lname VARCHAR(16),
                                    email VARCHAR(255) UNIQUE KEY,
                                    pass VARCHAR(255),
                                    gender ENUM('m','f') NOT NULL,
                                    signup DATETIME NOT NULL,
                                    lastlogin DATETIME NOT NULL,
                                    activated ENUM('0','1') NOT NULL DEFAULT '0'
                                    )";
$query = mysqli_query($db_con, $tbl_members);
if($query === TRUE)
{
    echo "<br/>Table 'members' created! :)<br/>";
}
else
{
    echo "<br/>Table 'members' NOT created :(<br/>";
}

////////////////////////////////////////////////////////////////////////////////////////
$tbl_messages = "CREATE TABLE IF NOT EXISTS messages(
                                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                    auth VARCHAR(16),
                                    recip VARCHAR(16),
                                    pm CHAR(1),
                                    tim INT UNSIGNED,
                                    message VARCHAR(4096)   
                                    )";
$query = mysqli_query($db_con, $tbl_messages);
if($query === TRUE)
{
    echo "<br/>Table 'messages' created! :)<br/>";
}
else
{
    echo "<br/>Table 'messages' NOT created :(<br/>";
}


///////////////////////////////////////////////////////////////////////////////////////
$tbl_friends = "CREATE TABLE IF NOT EXISTS friends(
                                    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    user VARCHAR(16),
                                    friend VARCHAR(16),
                                    datemade DATETIME NOT NULL,
                                    accepted ENUM('0','1') DEFAULT '0'
                                    )";
$query = mysqli_query($db_con, $tbl_friends);
if($query === TRUE)
{
    echo "<br/>Table 'friends' created! :)<br/>";
}
else
{
    echo "<br/>Table 'friends' NOT created :(<br/>";
}

//////////////////////////////////////////////////////////////////////////////////////
$tbl_profiles = "CREATE TABLE IF NOT EXISTS profiles(
                                    user VARCHAR(16),
                                    text VARCHAR(4096)
                                    )";
$query = mysqli_query($db_con, $tbl_profiles);
if($query === TRUE)
{
    echo "<br/>Table 'profiles' created! :)<br/>";
}
else
{
    echo "<br/>Table 'profiles' NOT created :(<br/>";
}

?>
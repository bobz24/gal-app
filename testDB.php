<?php
include_once "./includes/db_con.php";

$tbl = "CREATE TABLE IF NOT EXISTS test (col_1 VARCHAR(12))";
$query = mysqli_query($db_con, $tbl);
if($query === TRUE)
{
    echo "<br/>Table created! :)<br/>";
}
else
{
    echo "<br/>Table NOT created :(<br/>";
}
?>
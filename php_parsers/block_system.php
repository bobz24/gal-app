<?php //user blocking mechanism
include_once '../includes/check_login_status.php';
if($user_ok != TRUE || $log_username == ""){
    exit();
}
?>
<?php
if(isset($_POST['type']) && isset($_POST['blockee'])){
    $blockee = preg_replace('#[^a-z0-9]#i', '', $_POST['blockee']);
    //----check if blockee exists----//
    $sql = "SELECT COUNT(id) FROM members WHERE user = '$blockee' AND activated = '1' LIMIT 1";
    $query = mysqli_query($db_con, $sql);
    $exist_count = mysqli_fetch_row($query);
    if($exist_count[0] < 1){
        mysqli_close($db_con);
        die("$blockee does not exist.");
    }

    //----check to see if blockee is already blocked----//
    $sql = "SELECT id FROM blockedusers WHERE blocker = '$log_username' AND blockee = '$blockee' LIMIT 1";
    $query = mysqli_query($db_con, $sql);
    $numrows = mysqli_num_rows($query);
    
    if($_POST['type'] == "block"){
        if($numrows > 0){
            mysqli_close($db_con);
            die("You already have this member blocked.");
        }
        else{
            $sql = "INSERT INTO blockedusers(blocker, blockee, blockdate) VALUES('$log_username','$blockee',now())";
            $query = mysqli_query($db_con, $sql);
            mysqli_close($db_con);
            die("blocked_ok");
        }
    }
    else if($_POST['type'] == "unblock"){
        if($numrows == 0){
             mysqli_close($db_con);
            die("You do not have this user blocked, therefore we cannot unblock them.");
        }
        else{
            $sql = "DELETE FROM blockedusers WHERE blocker = '$log_username' AND blockee = '$blockee' LIMIT 1";
            $query = mysqli_query($db_con,$sql);
            mysqli_close($db_con);
            die("unblocked_ok");
        }
    }
}
?>
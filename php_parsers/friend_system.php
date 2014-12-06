<?php //friend system
include_once '../includes/check_login_status.php';
if($user_ok != TRUE || $log_username == ""){
    exit();
}
?>
<?php
if(isset($_POST['type']) && isset($_POST['user'])){
    $user = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
    $sql = "SELECT COUNT(id) FROM members WHERE user = '$user' AND activated ='1' LIMIT 1";
    $query = mysqli_query($db_con,$sql);
    $exist_count = mysqli_fetch_row($query);
    if($exist_count[0] < 1){
        mysqli_close($db_con);
        die("$user does not exist.");
    }
    
    if($_POST['type'] == "friend"){
        //----check number of friends----//
        $sql = "SELECT id FROM friends WHERE user = '$user' AND accepted = '1' OR friend = '$user' AND accepted = '1'";
        $query = mysqli_query($db_con,$sql);
        $friend_count = mysqli_fetch_row($query);
        //----check if the owner has blocked the viewer----//
        $sql = "SELECT COUNT(id) FROM blockedusers WHERE blocker = '$user' AND blockee = '$log_username' LIMIT 1";
        $query = mysqli_query($db_con,$sql);
        $blockcount1 = mysqli_fetch_row($query);
        //----check if the viewer has blocked the owner----//
        $sql = "SELECT COUNT(id) FROM blockedusers WHERE blocker = '$log_username' AND blockee = '$user' LIMIT 1";
        $query = mysqli_query($db_con,$sql);
        $blockcount2 = mysqli_fetch_row($query);
        //----check if the viewer is already friends with owner----//
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$log_username' AND friend = '$user' AND accepted = '1' LIMIT 1";
        $query = mysqli_query($db_con,$sql);
        $row_count1 = mysqli_fetch_row($query);
        //----check if the owner is already friends with viewer----//
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$user' AND friend = '$log_username' AND accepted = '1' LIMIT 1";
        $query = mysqli_query($db_con,$sql);
        $row_count2 = mysqli_fetch_row($query);
        //----check if viewer has a pending request sent to owner----//
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$log_username' AND friend = '$user' AND accepted = '0' LIMIT 1";
        $query = mysqli_query($db_con,$sql);
        $row_count3 = mysqli_fetch_row($query);
        //----check if owner has a pending request sent to viewer----// 
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$user' AND friend = '$log_username' AND accepted = '0' LIMIT 1";
        $query = mysqli_query($db_con,$sql);
        $row_count4 = mysqli_fetch_row($query);
        
        //----check number of friends----//
        if($friend_count[0] > 99){
            mysqli_close($db_con);
            die("$user currently has maximum number of friends and cannot accept more.");
        }
        //----check if the owner has blocked the viewer----//
        else if($blockcount1[0] > 0){
            mysqli_close($db_con);
            die("$user has blocked you. You cannot add them as a friend at the moment.");
        }
        //----check if the viewer has blocked the owner----//
        else if($blockcount2[0] > 0){
            mysqli_close($db_con);
            die("You must first unblock $user in order to be friends with them.");
        }
        //----check if the viewer is already friends with owner----//
        else if($row_count1[0] > 0 || $row_count2[0] > 0){
            die("You are already friends with $user");
        }
        //----check if viewer has a pending request sent to owner----//
        else if($row_count3[0] > 0){
            die("You have a pending friend request already sent to $user");
        }
        //----check if owner has a pending request sent to viewer----// 
        else if($row_count4[0] > 0){
            die("$user has already sent you a friend request. Check your friend requests.");
        }
        else{
            $sql = "INSERT INTO friends(user, friend, datemade) VALUES('$log_username','$user',now())";
            $query = mysqli_query($db_con, $sql);
            mysqli_close($db_con);
            die("friend_request_sent");
        }
        
    }
    else if($_POST['type'] == "unfriend"){
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$log_username' AND friend = '$user' AND accepted = '1' LIMIT 1";
        $query = mysqli_query($db_con, $sql);
        $row_count1 = mysqli_fetch_row($query);
        
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$user' AND friend = '$log_username' AND accepted = '1' LIMIT 1";
        $query = mysqli_query($db_con, $sql);
        $row_count2 = mysqli_fetch_row($query);
        
        if($row_count1[0] > 0){
            $sql = "DELETE FROM friends WHERE user = '$log_username' AND friend = '$user' AND accepted = '1' LIMIT 1";
            $query = mysqli_query($db_con, $sql);
            mysqli_close($db_con);
            die("unfriend_ok");
        }
        else if($row_count2[0] > 0){
            $sql = "DELETE FROM friends WHERE user = '$user' AND friend = '$log_username' AND accepted = '1' LIMIT 1";
            $query = mysqli_query($db_con, $sql);
            mysqli_close($db_con);
            die("unfriend_ok");
        }
    
        else{
            mysqli_close($db_con);
            die("No friendship could be found between your account and $user, therefore we cannot unfriend you.");
        }
    }

    //code to cancel pending friend request
    else if($_POST['type'] == "cancel"){
        //check if row exists in friends table
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$log_username' AND friend = '$user' AND accepted = '0' LIMIT 1";
        $query = mysqli_query($db_con, $sql);
        $row_count1 = mysqli_fetch_row($query);
        
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$user' AND friend = '$log_username' AND accepted = '0' LIMIT 1";
        $query = mysqli_query($db_con, $sql);
        $row_count2 = mysqli_fetch_row($query);
        
        if($row_count1[0] > 0){
            $sql = "DELETE FROM friends WHERE user = '$log_username' AND friend = '$user' AND accepted = '0' LIMIT 1";
            $query = mysqli_query($db_con, $sql);
            mysqli_close($db_con);
            die("cancel_ok");
        }
        else if($row_count2[0] > 0){
            $sql = "DELETE FROM friends WHERE user = '$user' AND friend = '$log_username' AND accepted = '0' LIMIT 1";
            $query = mysqli_query($db_con, $sql);
            mysqli_close($db_con);
            die("cancel_ok");
        }
    
        else{
            mysqli_close($db_con);
            die("No friendship could be found between your account and $user, therefore we cannot unfriend you.");
        }
    }

}
?><?php  // Friend request accept/reject code
if(isset($_POST['action']) && isset($_POST['reqid']) && isset($_POST['user1'])){
    $reqid = preg_replace('#[^0-9]#', '', $_POST['reqid']);
    $user = preg_replace('#[^a-z0-9]#i', '', $_POST['user1']);
    $sql = "SELECT COUNT(id) FROM members WHERE user = '$user' AND activated = '1' LIMIT 1";
    $query = mysqli_query($db_con,$sql);
    $exist_count = mysqli_fetch_row($query);
    if($exist_count[0] < 1){
        mysqli_close($db_con);
        die("$user does not exist");
    }
    if($_POST['action'] == "accept"){
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$log_username' AND friend = '$user' AND accepted = '1' LIMIT 1";
        $query = mysqli_query($db_con, $sql);
        $row_count1 = mysqli_fetch_row($query);
        $sql = "SELECT COUNT(id) FROM friends WHERE user = '$user' AND friend = '$log_username' AND accepted = '1' LIMIT 1";
        $query = mysqli_query($db_con, $sql);
        $row_count2 = mysqli_fetch_row($query);
        if($row_count1[0] > 0 || $row_count2[0] > 0){
            mysqli_close($db_con);
            die("You are already friends with $user");
        }
        else{
            $sql = "UPDATE friends SET accepted = '1' WHERE id = '$reqid' AND user = '$user' AND friend = '$log_username' LIMIT 1";
            $query = mysqli_query($db_con, $sql);
            mysqli_query($db_con, "INSERT INTO notifications(username, initiator, app, note, date_time) VALUES('$user', '$log_username', 'Friend Request', '$log_username accepted your friend request', now())"); //added this
            mysqli_close($db_con);
            die("accept_ok");
        }
    }
    else if($_POST['action'] == "reject"){
        mysqli_query($db_con, "DELETE FROM friends WHERE id = '$reqid' AND user = '$user' AND friend = '$log_username' AND accepted = '0' LIMIT 1");
        mysqli_close($db_con);
        die("reject_ok");
    }
}
?>

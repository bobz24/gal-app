<?php
if(isset($_POST['flag']))
{
	$flag = $_POST['flag'];
	if($user_ok == true){
		$sql = "SELECT notescheck FROM members WHERE user = '$log_username' LIMIT 1";
		$query = mysqli_query($db_con,$sql);
		$row = mysqli_fetch_row($query);
		$notescheck = $row[0];
		$sql = "SELECT id FROM notifications WHERE username = '$log_username' AND date_time > '$notescheck' LIMIT 1";
		$query = mysqli_query($db_con,$sql);
		$numrows = mysqli_num_rows($query);
		//----For friend request notifications----//
		$sql = "SELECT id FROM friends WHERE friend = '$log_username' AND datemade > '$notescheck' AND accepted = '0' LIMIT 1";
		$query = mysqli_query($db_con,$sql);
		$friendrows = mysqli_num_rows($query);
		
		if($numrows == 0 && $friendrows == 0){
			die("notif_active");
		}
		else{
			die("notif_idle");
    	}
	}
}
?>
<?php //----Notifications system----//
include_once 'includes/check_login_status.php';
if($user_ok != true || $log_username == ""){
    header("location: ./index.php");
    exit();
}

$notification_list = "";
$sql = "SELECT * FROM notifications WHERE username LIKE BINARY '$log_username' ORDER BY date_time DESC";
$query = mysqli_query($db_con, $sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	$notification_list = "You do not have any notifications.";
}
else{
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
	{
		$noteid = $row['id'];
		$initiator = $row['initiator'];
		$app = $row['app'];
		$note = $row['note'];
		$date_time = $row['date_time'];
		$date_time = strftime("%d %b %Y", strtotime($date_time));
		$notification_list .= '<div id="'.$date_time.'"><b>'.$date_time.'</b><br/><a href="member.php?u='.$initiator.'"><b>'.$initiator.'</b></a> | '.$app.'<br/>'.$note.'<br/></p></div>';
		/*for($i=0;$i<$numrows;$i++){
			$notification_list .= '<div class="alert-info" id="notes_'.$row['id'].'_'.$date_time.'"><b>'.$date_time.'</b><br/><a href="member.php?u='.$initiator.'"><b>'.$initiator.'</b></a> | '.$app.'<br/>'.$note.'<br/></p></div>';
		}*/
	}
}
mysqli_query($db_con, "UPDATE members SET notescheck = now() WHERE user = '$log_username' LIMIT 1");
?>

<?php //====Friend Request notification system----//
$friend_req = "";
$sql = "SELECT * FROM friends WHERE friend = '$log_username' AND accepted = '0' ORDER BY datemade ASC";
$query = mysqli_query($db_con,$sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	$friend_req = "No friend requests.";
}
else{
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
	{
		$reqID = $row['id'];
		$user1 = $row['user'];
		$datemade = $row['datemade'];
		$datemade = strftime("%d %b %Y", strtotime($datemade));
		$thumbquery = mysqli_query($db_con,"SELECT avatar FROM members WHERE user = '$user1' LIMIT 1");
		$thumbrow = mysqli_fetch_row($thumbquery);
		$user1avatar = $thumbrow[0];
		$user1pic = '<img class="media-object user_pic" src="user/'.$user1.'/'.$user1avatar.'" alt="'.$user1.'">';
		if($user1avatar == NULL){
			$user1pic = '<img class="media-object user_pic" src= "images/avatar_m.jpg" alt="'.$user1.'">';
		}
		$friend_req .= '<div class="media" id="friendreq_'.$reqID.'">';
		$friend_req .= '<a class="pull-left" href="member.php?u='.$user1.'">'.$user1pic.'</a>';
		$friend_req .= '<div class="media-body" id="user_info_'.$reqID.'"><a href="member.php?u='.$user1.'"><b>'.$user1.'</b></a> wants to add you as a friend<br /><p class="text-muted timestamp">'. $datemade. '</p>';
		$friend_req .= '<button class="btn btn-sm btn-info" onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">Accept</button>&nbsp;';
		$friend_req .= '<button class="btn btn-sm btn-default" onclick="friendReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">Reject</button><br/>';
		$friend_req .= '</div>';
		$friend_req .= '</div>';
		
	}
}
?>
<!DOCTYPE html>
	<html>
		<head>
			<title>Notifications</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="bs/css/bootstrap.min.css" rel="stylesheet" type="text/css">
			<link href="bs/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css">
			<link href="css/main.css" rel="stylesheet" type="text/css">
			
		</head>
		<body>
			<div class="container">
				<?php include_once "./templates/template_pageHeader.php";?>
					<div class="content">
						<div class="row">
							<div class="col-md-8 well">
								<div class="page-header">
									<h1>Notifications</h1>
								</div>
								
								    
								    <?php echo $notification_list; ?>
								
								
							</div>
							<div class="col-md-3 well well-sm">
								<h1><small>Friend Requests</small></h1>
								<?php echo $friend_req; ?>
							</div>
						</div>
					</div>
				<?php include_once "./templates/template_pageFooter.php";?>
			</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="bs/js/bootstrap.min.js"></script>
		<script src="js/ajax.js"></script>
		<script src="js/OSC.js"></script>
		<script>
			function friendReqHandler(action,reqid,user1,elem){
				var conf = confirm("Press OK to '"+action+"' this friend request.");
				if (conf != true) {
					return false;
				}
				O(elem).innerHTML = "processing ...";
				var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
				ajax.onreadystatechange = function(){
					if (ajaxReturn(ajax) == true) {
						if (ajax.responseText == "accept_ok") {
							O(elem).innerHTML = "<b>Request Accepted!</b><br/>You are now friends";
						}
						else if (ajax.responseText == "reject_ok") {
							O(elem).innerHTML = "<b>Request rejected</b><br/>You rejected the friend request";
						}
						else{
							O(elem).innerHTML = ajax.responseText;
						}
					}
				}
				ajax.send("action="+action+"&reqid="+reqid+"&user1="+user1);
			}
		</script>
		</body>
	</html>

<?php
include_once './includes/check_login_status.php';

//-------Initialize local vars that page might echo----// <----This is working as expected, with 1 bug: Firefox displays "User data not found :(" at first log in.  
$u = "";
$fullName = "";
$sex = "Male";
$joindate = "";
$lastsession = "";
$email = "";
$friend_count = "";
$avatar_form = "";
$edit_pic_link = "";

if(isset($_GET['u'])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
}
else{
	header("location: index.php");
	exit();
}

//---Select user from the table---//
$sql = "SELECT * FROM members WHERE user = '$u' AND activated ='1' LIMIT 1"; //change @12:37AM 22/3/14 ~added "activated" condition
$user_query = mysqli_query($db_con, $sql);
//--make sure user exists---//
$numrows = mysqli_num_rows($user_query);
if($numrows < 1){
	die("User data not found :(" .$numrows);
}

//---check to see if viewer is page owner---//
$isOwner = "No";
if($u == $log_username && $user_ok == true){
	$isOwner = "Yes";
	$edit_pic_link = '<a href="#" data-toggle="modal" data-target=".bs-example-modal-sm">Edit profile photo</a>';
	$profile_pic_btn = '<a href=# onclick = "return false;" onmousedown = "toggleElement(\'avatar_form\')">Update profile photo</a>';
	$avatar_form  = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$avatar_form .= '<h4>Change your avatar</h4><hr>';
	$avatar_form .= '<input type="file" title="Choose photo" name="avatar" required><br/>';
	$avatar_form .= '<p><input type="submit" class="btn btn-info" value="Upload"></p>';
	$avatar_form .= '</form>';
}	

//=======MAJOR CHANGE========//change @12:42AM 22/3/14 ~removed the 'fetch user data' block from inside the if-block. This was a major screw up.
//UPDATE: @12:44AM 22/3/14 ~This was the faulty bit of code. Website now working as expected. Other users' data now visible to logged in user :) 

//----Fetch user data from db----//
while($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC))
{
	$profile_id = $row["id"];
	$fname = $row["fname"];
	$lname = $row["lname"];
	$gender = $row["gender"];
	$avatar = $row["avatar"];
	$signup = $row["signup"];
	$lastlogin = $row["lastlogin"];
	$email = $row["email"];
	//--convert dates to simple format--//
	$joindate = strftime("%d %b %Y", strtotime($signup));
	$lastsession = strftime("%d %b %Y", strtotime($lastlogin));
	if ($gender == "f"){
		$sex = "Female";
	}
	$fullName = $fname ." ". $lname;

	$profile_pic = 'user/'.$u.'/'.$avatar.'';
	if($avatar == NULL){
	$profile_pic = 'images/avatar_m.jpg';
	}
}
?>
<?php
$isFriend = false;
$ownerBlockViewer = false;
$viewerBlockOwner = false;
$reqSentOwner = false;
$reqSentViewer = false;

if($u != $log_username && $user_ok == true){
	$friend_check = "SELECT id FROM friends WHERE user = '$log_username' AND friend = '$u' AND accepted = '1' OR user = '$u' AND friend = '$log_username' AND accepted = '1' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_con,$friend_check)) > 0){
		$isFriend = true;
	}
	$block_check1 = "SELECT id FROM blockedusers WHERE blocker = '$u' AND blockee = '$log_username' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_con,$block_check1)) > 0){
		$ownerBlockViewer = true;
	}
	$block_check2 = "SELECT id FROM blockedusers WHERE blocker = '$log_username' AND blockee = '$u' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_con,$block_check2)) > 0){
		$viewerBlockOwner = true;
	}
	
	$req_check = "SELECT id FROM friends WHERE user = '$log_username' AND friend = '$u' AND accepted = '0' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_con,$req_check)) > 0){
		$reqSentViewer = true;
	}
	$req_check = "SELECT id FROM friends WHERE user = '$u' AND friend = '$log_username' AND accepted = '0' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_con,$req_check)) > 0){
		$reqSentOwner = true;
	}
}
?>
<?php
$friend_button = '<button type="button" class="btn btn-default disabled"><span class="glyphicon glyphicon-plus-sign"></span> Add as friend</button>';
$block_button = '<button type="button" class="btn btn-default disabled"><span class="glyphicon glyphicon-ban-circle"></span> Block user</button>';
// LOGIC FOR FRIEND BUTTON
if($isFriend == true){
	$friend_button = '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-ok"></span> Friends</button><ul class="dropdown-menu" role="menu">
    <li><a href=# onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')"><span class="glyphicon glyphicon-remove"></span> Unfriend '.$u.'</a></li></ul>';
}
else if($user_ok == true && $u != $log_username && $ownerBlockViewer == false){
	$friend_button = '<button type="button" class="btn btn-default" onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')" ><span class="glyphicon glyphicon-plus-sign"></span> Add as friend</button>';
}
if($reqSentViewer == true){
	$friend_button = '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-check"></span> Friend Request Sent</button><ul class="dropdown-menu" role="menu"><li><a href="#" onclick="friendToggle(\'cancel\',\''.$u.'\',\'friendBtn\')"><span class="glyphicon glyphicon-remove"></span> Cancel request</a></li></ul>';
//change ~ @11:30AM 01/06/14 -- added "cancel" onclick event
}
else if($reqSentOwner == true){
	$friend_button = '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-check"></span>Confirm Friend Request</button><ul class="dropdown-menu" role="menu"><li><a href="#" onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Accept friend request</a></li><li><a href="#" onclick="friendToggle(\'cancel\',\''.$u.'\',\'friendBtn\')"><span class="glyphicon glyphicon-remove"></span> Cancel request</a></li></ul>';
//change ~ @11:30AM 01/06/14 -- added "cancel" onclick event
}
// LOGIC FOR BLOCK BUTTON
if($viewerBlockOwner == true){
	$block_button = '<button type="button" class="btn btn-danger" onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')">Unblock User</button>';
}
else if($user_ok == true && $u != $log_username){
	$block_button = '<button type="button" class="btn btn-danger" onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')"><span class="glyphicon glyphicon-ban-circle"></span> Block User</button>';
}
?>

<!DOCTYPE html>
	<html>
		<head>
			<title><?php echo $fullName ." ($u)"; ?></title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="bs/css/bootstrap.min.css" rel="stylesheet" type="text/css">
			<link href="bs/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css">
			<link href="css/main.css" rel="stylesheet" type="text/css">
			<link href="css/status.css" rel="stylesheet" type="text/css">
			<style type="text/css">
				#pic_box > a{
					display: none;
				}
				.img-thumbnail{padding:0px;}

				.user-heading{
					  margin: -1.73% -1.73% 20px;
					  padding-top: 30px;
					  padding-left: 20px;
					  background-color: #C5DEE3;
				}
			</style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>	
		</head>
		<body onload="showStatBox()">
			<div class="container">
				<?php include_once "./templates/template_pageHeader.php";?>
					<div class="content">
						<div id="main_row" class="row"><!--row1-->
							<div class="col-md-12 well" style="width: 92.5%;">
								<!--<div class="page-header">-->
									
									<!--profile photo div-->
									<div class="col-xs-6 col-md-3 pull-right">
										<a href="#" class="thumbnail profile-pic">
										  <img data-src="holder.js/100%x180" alt="<?php echo $u; ?>" src="<?php echo $profile_pic; ?>">
										</a>
										<div class="sm-link">
											<?php echo $edit_pic_link; ?>
											<!--dialog modal-->
											<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
												<div class="modal-dialog modal-sm">
												  <div class="modal-content">
												    <?php echo $avatar_form; ?>
												  </div>
												</div>
											</div><!--end of modal-->
										</div>
									</div><!--end of profile photo div-->
									
									<div class="user-heading page-header panel">
										<div class="name-nice">
											<h1><?php echo $fullName; echo "<small>&nbsp;($u)</small>";?></h1>
										</div>
									</div>
									<!--button spans-->
									<span id="btn" style="float: right;">
										<div class="btn-group">
											<span id="friendBtn"><?php echo $friend_button; ?></span>
											<span id="blockBtn"><?php echo $block_button; ?></span>
										</div>
									</span>
								<!--</div>-->
								<p>Is the viewer the page owner, logged in and verified?: <b><?php echo $isOwner;?></b></p>
								<p>Gender: <b><?php echo $sex; ?></b></p>
								<p>Email: <b><?php echo $email; ?></b></p>
								<p>Join date: <b><?php echo $joindate; ?></b></p>
								<p>Last login: <b><?php echo $lastsession; ?></b></p>
							</div>
						</div><!--end of main_row(1)-->
						<div id="friends_row" class="row">	
							<div class="col-md-3 well well-sm"><!--friends div-->
								<?php 	
									$view_all = '';
									$max = 9;
									$sql = "SELECT COUNT(id) FROM friends WHERE user = '$u' AND accepted = '1' OR friend = '$u' AND accepted = '1'";
									$query = mysqli_query($db_con,$sql);
									$query_count = mysqli_fetch_row($query);
									$friend_count = $query_count[0];
									
									if($friend_count > $max){
										$view_all = '<a href="#">View all</a>';
									}
									 ?>
                                <div class="user-heading page-header panel">
                                	<div class="name-nice">
                                		<h3>Friends (<?php echo $friend_count;?>)&nbsp<small><?php echo $view_all; ?></small></h3> 
                                	</div>
                            	</div>
								<div class="grid">	
									<?php $sql = "SELECT friend FROM friends WHERE user = '$u' AND accepted = '1' ORDER BY RAND() LIMIT 6";
									$query = mysqli_query($db_con, $sql);
									$added = array();
									$addedby = array();
									while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
										array_push($added, $row["friend"]);
									}
									$sql = "SELECT user FROM friends WHERE friend = '$u' AND accepted = '1' ORDER BY RAND() LIMIT 6";
									$query = mysqli_query($db_con, $sql);
									while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
										array_push($addedby, $row["user"]);
									}
									$friends = array_merge($added,$addedby);
									//print_r($friends);
									foreach($friends as $key => $user) {
										if(count($friends) < 1){
											echo "<p>No friends added yet</p>";
										}
										else{
											//$friendList = "<a href='member.php?u=$user'>$user</a><br/>";
											//sql for avatars
											$sql = "SELECT avatar FROM members WHERE user = '$user' LIMIT 1";
											$query = mysqli_query($db_con, $sql);
											$result = mysqli_fetch_array($query);
											$user1avatar = $result[0];
											$user1pic = '<a class="img-thumbnail sm-profile-pic" style="height: 70px; width: 70px; overflow: hidden;" href="member.php?u='.$user.'"><img class="img-thumbnail" src="user/'.$user.'/'.$user1avatar.'" alt="'.$user.'" title="'.$user.'"><div class="name-overlay"><span>'.$user.'</span></div></a>';
											if($user1avatar == NULL){
												$user1pic = '<a class="img-thumbnail sm-profile-pic" style="height: 70px; width: 70px; overflow: hidden;" href="member.php?u='.$user.'"><img class="img-thumbnail" src= "images/avatar_m.jpg" alt="'.$user.'" title="'.$user.'"><div class="name-overlay"><span>'.$user.'</span></div></a>';
											}
											echo $user1pic."<br/>";
									    }
									}
										  
									
									/*if($usernumrows > 0){
										echo "<b>Data found!</b><br/>";
										while($row = mysqli_fetch_array($query, MYSQLI_NUM))
										echo "<a href='//member.php?u=$row[0]'>$row[0]</a><br/>";
									}
									else "<b>Data not found :(</b>"; */
								?>
                                </div>
							</div> <!-- col-well-friends-->
							<?php include_once "./templates/template_status.php"; ?>
						</div><!--end of friends_row(2)-->
						
						<div id="other_row" class="row"><!--start of row3-->
							<div class="col-md-3 well well-sm">
								<h1><small>Other members</small></h1>
								<?php $sql = "SELECT user FROM members WHERE user NOT LIKE '$log_username' AND user NOT LIKE '$u' ORDER BY RAND()";
									$query = mysqli_query($db_con, $sql);
									$all_users = array();
									$all_friends = array();
									while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
										array_push($all_users, $row["user"]);
									}
									$sql = "SELECT user, friend FROM friends WHERE user = '$log_username' OR friend = '$log_username' AND accepted = '1' ORDER BY RAND()";
									$query = mysqli_query($db_con, $sql);
									while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
										array_push($all_friends, $row["user"]);
										array_push($all_friends, $row["friend"]);
									}
									//$others = array_unique($all_users);
									//print_r($all_friends);
									//print_r($all_users);
									$others = array_diff($all_users, $all_friends);
									//print_r($others);
									foreach($others as $key => $user) {
										//if($user != $log_username){
											//print_r($others);
											echo "<a href='member.php?u=$user'>$user</a><br/>";
									      //}
									}
									
									
									//$usernumrows = mysqli_num_rows($query);
									//$userlist = "";
									/*if($usernumrows > 0){
										//echo "<b>Data found!</b><br/>";
										while($row = mysqli_fetch_array($query, MYSQLI_NUM))
										echo "<a href='member.php?u=$row[0]'>$row[0]</a><br/>";
									}
									else "<b>Data not found :(</b>";*/
								?>
								
							</div><!-- col-well-others-->
						</div><!--end of other_row(3)-->
						
					</div><!--content div-->
				<?php include_once "./templates/template_pageFooter.php";?>
			</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="bs/js/bootstrap.min.js"></script>
		<script src="js/ajax.js"></script>
		<script src="js/OSC.js"></script>
		<script src="js/img_ts.js"></script>
		
		<script>
			function friendToggle(type, user, elem){
				var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $u; ?>.");
				if (conf!= true) {
					return false;
				}
				O(elem).innerHTML = "Please wait...";
				var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
				ajax.onreadystatechange = function(){
					if (ajaxReturn(ajax) == true) {
						if (ajax.responseText == "friend_request_sent") {
							O(elem).innerHTML = '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-check"></span> Friend Request Sent</button><ul class="dropdown-menu" role="menu"><li><a href="#" onclick="friendToggle(\'unfriend\',\'<?php echo $u; ?>\',\'friendBtn\')">Cancel request</a></li></ul>';
						}
						//change ~ @11:34AM 01/06/14 -- added "cancel_ok" clause
						else if (ajax.responseText == "unfriend_ok" || ajax.responseText == "cancel_ok") {
							O(elem).innerHTML = '<button type="button" class="btn btn-default" onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Add as friend</button>';
						}
						else{
							alert(ajax.responseText);
							O(elem).innerHTML - "Try again later";
						}
					}
				}
				ajax.send("type="+type+"&user="+user);
			}
			
			function blockToggle(type,blockee,elem){
				var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $u; ?>.");
				if (conf!= true) {
					return false;
				}
				var elem = O(elem);
				elem.innerHTML = "Please wait...";
				var ajax = ajaxObj("POST", "php_parsers/block_system.php");
				
				ajax.onreadystatechange = function(){
					if (ajaxReturn(ajax) == true) {
						if (ajax.responseText == "blocked_ok") {
							elem.innerHTML = '<button type="button" class="btn btn-default" onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\')">Unblock User</button>';
						}
						else if (ajax.responseText == "unblocked_ok") {
							elem.innerHTML = '<button type="button" class="btn btn-default" onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\')">Block user</button>';
						}
						else{
							alert(ajax.responseText);
							elem.innerHTML - "Try again later";
						}
					}
				}
				ajax.send("type="+type+"&blockee="+blockee);
			}	
			
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
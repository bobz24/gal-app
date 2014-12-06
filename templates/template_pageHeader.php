<?php //Logic for dynamic header links
//it is necessary for files that include this page to also have included check_login_status.php file

$profile = "<a href='./member.php?u=$log_username'>$log_username</a>";
$signup = '<a href="./signup.php">Sign up</a>';
$login = '<a href="./login.php">Log in</a>';
$logout = '<a href="./logout.php">Log out</a>';
$home = '<li><a title="Home" href="./index.php"><span class="glyphicon glyphicon-home"></span></a></li>';
$notflag = '<li><a href="./notifications.php"><span class="glyphicon glyphicon-flag"></span></a></li>'; //removed id from <li>
$search_form = "";

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
        $notflag = '<li><a href="./notifications.php" title="Your notifications and friend requests"><span class="glyphicon glyphicon-flag"></span></a></li>'; 
    }
    else{
        $notflag = '<li><a href="./notifications.php" title="You have new notifications"><span id="notesactive" class="glyphicon glyphicon-flag notif_anim"></span></a></li>';
    }
	
	//search form
	$search_form = '<form class="navbar-form navbar-left" role="search" action="search.php" method="GET"><div class="form-group">';
	$search_form .= '<input type="text" id="search_form" name="query" class="form-control" placeholder="Search username [beta]"></div><button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button></form>';
}
?>
<link href='http://fonts.googleapis.com/css?family=Sansita+One' rel='stylesheet' type='text/css'> <!--added these styles and webfont(s)-->
<link href='./css/logo.css' rel='stylesheet' type='text/css'>
<!--<script onload="flag()">
function flag(){
	var intervalID = setInterval(checkNotif, 1000);
}
function checkNotif(flag){
	var flag = O(notif);
	flag.innerHTML = '<a href="./notifications.php"><span class="glyphicon glyphicon-flag"></span></a>';
	
	var ajax = ajaxObj("POST", "php_parsers/notifs.php");
	ajax.onreadystatechange = function(){
						if (ajaxReturn(ajax) == true) {
							if (ajax.responseText == "notif_active") {
								flag.innerHTML = '<a href="./notifications.php"><span id="notesactive" class="glyphicon glyphicon-flag"></span></a>';
								console.log(ajax.responseText);
							}
							else {
								flag.innerHTML = '<a href="./notifications.php"><span class="glyphicon glyphicon-flag"></span></a>';
								console.log(ajax.responseText);
							}
							
					}
					ajax.send("flag"+flag);
	}
}
</script>-->
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				</button> <a class="navbar-brand" id="logoTop" href="./index.php">GetaLife [beta]</a> <!--added google web font to logo-->
			</div>
			<div class="navbar-collapse collapse" style="height: 1px;">
				<ul class="nav navbar-nav pull-right">
					<!--<li><a href="./index.php"><span class="glyphicon glyphicon-home"></span></a></li>-->
                                        <?php if($user_ok == true) echo $search_form; ?>
										<?php if($user_ok == true) echo $notflag; ?>
                                        <?php if($user_ok == true) echo $home; ?>
					<li style="font-family: 'Sansita One', Arial, cursive;"><?php if($user_ok == true) echo $profile."</li>"; else echo "<li>". $signup; ?></li>
                                        <!--<li><a href="./signup.php">Sign up</a></li>
					<li><a href="./login.php">Log in</a></li>
                                        <li><a href="./logout.php">Log out</a></li>-->
                                        <li><?php if($user_ok == true) echo $logout; else echo $login; ?></li>
				</ul>
			</div>
		</div>
</div>
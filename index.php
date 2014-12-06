<?php
include_once './includes/check_login_status.php';
$login_link = '<p>Please <a href="login.php">log in</a> or <a href="signup.php">sign up</a> to continue</p>';
$logged_in_link = '<p>This area is placeholder. This will contain user/friends data feeds, recent activity etc.<br/> <a href="member.php?u='.$log_username.'">Click here</a> to go to your profile.</p>';
if($user_ok == true){
	$title = "Home";
}
else { $title = "Welcome to GetaLife";}
?> 
<!DOCTYPE html>
	<html>
		<head>
			<title><?php echo $title;?></title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="bs/css/bootstrap.min.css" rel="stylesheet" type="text/css">
			<link href="bs/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css">
			<link href='./css/logo.css' rel='stylesheet' type='text/css'>
            <link href="css/main.css" rel="stylesheet" type="text/css">
			<style type="text/css">
				.content{
					margin: 70px auto;
				}
			</style>
			
		</head>
		<body>
			<div class="container">
				<?php include_once "./templates/template_pageHeader.php";?>
					<div class="content">
						<div class="jumbotron">
							<h1>Welcome <?php if(!$user_ok) echo 'to <span id="logo">GetaLife.com!</span>';?> <?php if($user_ok == true) echo '<span id="logo">'.$log_username.'</span>'; ?></h1>
							<?php if($user_ok == true) echo $logged_in_link; else echo $login_link; ?> 
						</div>
					</div>
				<?php include_once "./templates/template_pageFooter.php";?>
			</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="bs/js/bootstrap.min.js"></script>
		</body>
	</html>

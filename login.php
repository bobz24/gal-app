<?php
//session_start();  ~already included check_login_status.php
include_once './includes/check_login_status.php';
//check session--if user is already logged in, redirect to their homepage
if(isset($_SESSION['username'])){
	header("location: member.php?u=".$_SESSION['username']);
	exit();
}
?><?php
//Ajax respond code for login <----This is working perfectly!
if(isset($_POST['u'])){
	//------------------open db connection-------------------//
	include_once "./includes/db_con.php";
	//-------------gather login data into local variables----//
	$un = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	$pw = $_POST['p'];
	//-------------FORM VALIDATION & ERROR HANDLING CODE---------------------//
	if($un == "" || $pw == ""){
		die("login_failed");
	}
	else{
		//---------Begin DB queries-----------------//
		$sql = "SELECT id, user, pass FROM members WHERE user = '$un' AND activated = '1' LIMIT 1"; //change @12:19AM 22/3/14 ~added "activated" condition
		$query = mysqli_query($db_con, $sql);
		$row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_un = $row[1];
		$db_pw = $row[2];
			
		if($db_pw != $pw){
			die("login_failed");
		}
		else{
			//----SET UP SESSION VARIABLES-----//
			$_SESSION['userid'] = $db_id;
			$_SESSION['username'] = $db_un;
			$_SESSION['password'] = $db_pw;
			
			//----SET UP COOKIES--------------//
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("user", $db_un, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("pass", $db_pw, strtotime( '+30 days' ), "/", "", "", TRUE); 
			
			//----UPDATE last login date/time---//
			$sql = "UPDATE members SET lastlogin = now() WHERE user = '$db_un' LIMIT 1"; //change @12:22AM 22/3/14 ~removed quotes from "now()" function
			$query = mysqli_query($db_con, $sql);
			
			echo $db_un;
			exit();
		}
			
	}
	die("login_failed");
		
}
	
?>


<!DOCTYPE html>
	<html>
		<head>
			<title>GetaLife || Log in</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="bs/css/bootstrap.min.css" rel="stylesheet" type="text/css">
			<link href="bs/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css">
			<link href="css/main.css" rel="stylesheet" type="text/css">
			<style type="text/css">
				.well, .form-signin {margin: auto 25% 25%;}
				.form-control {margin: 20px auto 20px;}
				#logo {
					margin: 25px auto;
					font-size: xx-large;
					text-align: center;
				}
			</style>
			
		</head>
		<body>
			<div class="container">
				<?php include_once "./templates/template_pageHeader.php";?>
					<div class="content">
						<div id="logo">GetaLife</div>
						<div class="well">
							<form id="loginForm" class="form-signin" role="form" onsubmit="return false;">
							   <h2 class="form-signin-heading">Please sign in</h2>
							   <input class="form-control" placeholder="Username" id="user" name='user' required autofocus type="text">
							   <input class="form-control" placeholder="Password" id="pass" name='pass' required type="password">
							   <label class="checkbox">
							     <input value="remember-me" type="checkbox"> Remember me
							   </label>
							   <span id='status'></span>
							   <button class="btn btn-lg btn-primary btn-block" type="submit" onclick='login()'>Sign in</button>
							</form>
					      </div> <!-- jumbo-->
					</div>
				<?php include_once "./templates/template_pageFooter.php";?>
			</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="bs/js/bootstrap.min.js"></script>
		<script src="js/ajax.js"></script>
		<script src="js/OSC.js"></script>
		<script>
			function login(){
				console.log("function login start");
				var u = O("user").value;
				var p = O("pass").value;
				var status = O("status");
				if (u == "" || p == "") {
					 status.innerHTML = "Not all the fields were entered."
				}
				else{
					var ajax = ajaxObj("POST", "login.php");
					ajax.onreadystatechange = function() {
						if (ajax.responseText == "login_failed") {
							info.innerHTML = "Login unsuccessful. Please try again.";
							console.log(ajax.responseText);
						}
						else{
							window.location = "member.php?u="+ajax.responseText;
							console.log("function login url received");
							
						}
					
					}
					ajax.send("u="+u+"&p="+p);
				}
				console.log("function login end");
			}
		</script>
		</body>
	</html>

<?php
//session_start(); ~already included check_login_status.php
include_once './includes/check_login_status.php';
//if user is logged in, redirect them
if(isset($_SESSION['username'])){
	$userRedir = $_SESSION['username'];
	echo "you are already logged in dufus. <a href='member.php?u=$userRedir'>Go back</a>";
	//header("location: message.php?msg=NO to that weenis");
	exit();
}
?><?php
//Ajax check for username availability <----This is working perfectly!
if(isset($_POST['usernamecheck'])){
	include_once "./includes/db_con.php";
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM members WHERE user='$username' LIMIT 1";
	$query = mysqli_query($db_con, $sql);
	$uname_check = mysqli_num_rows($query);
	
	if(strlen($username) < 3 || strlen($username) > 16){
		die("<strong style='color: red;'>Username must be 3 - 16 characters</strong>");
	}
	if(is_numeric($username[0])){
		die("<strong style='color: red;'>Username must begin with a letter</strong>");
	}
	if($uname_check < 1){
		die("<strong style='color: green;'>Username ". $username ." is OK</strong>");
	}
	else{
		die("<strong style='color: red;'>Sorry, the username ". $username ." is already taken</strong>");
	}
}
?><?php
//Ajax code for signup <----This is working perfectly!
if(isset($_POST['uname'])){
	//connect to db
	include_once "./includes/db_con.php";
	//initialize local variables with POST data
	$fn = preg_replace('#[^a-z0-9]#i', '', $_POST['fname']);
	$ln = preg_replace('#[^a-z0-9]#i', '', $_POST['lname']);
	$em = mysqli_real_escape_string($db_con, $_POST['email']);
	$un = preg_replace('#[^a-z0-9]#i', '', $_POST['uname']);
	$sex = preg_replace('#[^a-z0-9]#i', '', $_POST['sex']);
	$pw = $_POST['pass'];
	//checks for username and email
	$sql = "SELECT id FROM members WHERE user = '$un' LIMIT 1";
	$query = mysqli_query($db_con, $sql);
	$u_check = mysqli_num_rows($query);
	//-----------------------------------------------------------//
	$sql = "SELECT id FROM members WHERE email = '$em' LIMIT 1";
	$query = mysqli_query($db_con, $sql);
	$e_check = mysqli_num_rows($query);
	//------SERVER SIDE FORM VALIDATION CODE---------------------//
	if ($fn == "" || $ln == "" || $un == "" || $em == "" || $pw == "" || $sex == ""){
		die("Not all the fields were entered. Please check form.");
	}
	elseif(strlen($un) < 3 || strlen($un) > 16){
		die("Username must be between 3 and 16 characters long.");
	}
	elseif (is_numeric($un[0])){
		die("Username must begin with a letter");
	}
	elseif ($u_check > 0){
		die("The username you entered is already taken.");
	}
	elseif ($e_check > 0){
		die("The email you entered is already in the system.");
	}
	//---------Begin insertion of form data into db--------------//
	else{
		$sql = "INSERT INTO members (user, fname, lname, email, pass, gender, signup, lastlogin, notescheck) VALUES('$un','$fn','$ln','$em','$pw', '$sex', now(),now(),now())"; //--Change @12:03AM 22/3/14 ~added notescheck field to table
		$query = mysqli_query($db_con, $sql);
		$uid = mysqli_insert_id($db_con); //--<<----------<<----------------<<----MUST LOOK INTO THIS LATER
		//--------establish row in profiles table--------------------//
		$sql = "INSERT INTO useroptions (id, username, background) VALUES('$uid','$un','original')"; //--change @12:07AM 22/3/14 ~added useroptions (fieldnames)
		$query = mysqli_query($db_con, $sql);
		
		//---------create directory folders for each user------------//
		if(!file_exists("user/$un")){
			mkdir("user/$un", 0755, true);
		}
		die("signup_success");
		//exit();
	}
	exit();
}
//else echo "Something's broken. The code above is not working.";
//exit();
?>
<!DOCTYPE html>
	<html>
		<head>
			<title>GetaLife || Sign up</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="bs/css/bootstrap.min.css" rel="stylesheet" type="text/css">
			<link href="bs/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css">
			<link href="css/main.css" rel="stylesheet" type="text/css">
			<link href='bs/css/signin.css' rel='stylesheet' type="text/css">
			<style type='text/css'>
			    .form-control, .label, .radio {margin-bottom: 30px;}
			    .btn {margin-bottom: 20px; }
			</style>
		</head>
		<body>
			<div class="container">
				<?php include_once "./templates/template_pageHeader.php";?>
					<div class="content">
						<form id="signupform" class="form-signin" role="form" onsubmit="return false;">
							<h1 class="form-signin-heading">Sign up</h1>
							<input class="form-control" placeholder="First name" id="fname" name='fname' required autofocus type="text">
							<input class="form-control" placeholder="Last name" id="lname" name='lname' required type="text"><span id='unameStatus'></span>
							<input class="form-control" placeholder="Preferred username" id="user" name='user' onblur="checkUsername()" required type="text" data-container="body" data-toggle="popover" data-placement="right" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                            <select class="form-control dropdown" id="gender">
                            <option value="" class="dropdown-header disabled">Gender</option>
                            <option value="m">Male</option>
                            <option value="f">Female</option>
                            </select>
							<input class="form-control" style='margin-bottom:30px;' placeholder="Email" id="email" name='email' required type="email">
							<input class="form-control" placeholder="Password" id="pass" name='pass' required type="password">
							<!--<label class="checkbox">
							  <input value="remember-me" type="checkbox"> Remember me
							</label>-->
							<span id="info"></span>
							<button class="btn btn-lg btn-success btn-block" type="submit" onclick="signup()" >Sign up</button>
							<p>Already have an account? <a href='login.php'>Sign in</a></p>
						</form>
					</div>
				<?php include_once "./templates/template_pageFooter.php";?>
			</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="bs/js/bootstrap.min.js"></script>
		<script src="js/ajax.js"></script>
		<script src="js/OSC.js"></script>
		<script>
			function signup(){
				console.log("function signup start");
				var fname = O("fname").value;
				var lname = O("lname").value;
				var uname = O("user").value;
				var pass = O("pass").value;
				var email = O("email").value;
				var sex = O("gender").value;
				var info = O("info");
				if (fname == "" || lname == "" || uname == "" || email == "" || pass == "" || sex == "") {
					 info.innerHTML = "Not all the fields were entered."
				}
				else{
					var ajax = ajaxObj("POST", "signup.php");
					ajax.onreadystatechange = function() {
						if (ajax.responseText != "signup_success") {
							info.innerHTML = ajax.responseText;
							console.log("inside ajax responseText block");
							console.log(ajax.responseText);
						}
						else{
							console.log("after ajax responseText success");
							window.scrollTo(0,0);
							O("signupform").innerHTML = "Sign up successful. This **** works!<br/> <a href='login.php'>Click here</a> to go to activate your account and log in";
						}
					
					}
					ajax.send("fname="+fname+"&lname="+lname+"&uname="+uname+"&email="+email+"&pass="+pass+"&sex="+sex)
				}
				console.log("function signup end");
			}
			
			function checkUsername(){
				console.log("checkUsername function start");
				var u = O("user").value;
				if (u != "") {
					O("unameStatus").innerHTML = "Checking...";
					var ajax = ajaxObj("POST", "signup.php");
					ajax.onreadystatechange = function() {
						if (ajaxReturn(ajax) == true) {
							O("unameStatus").innerHTML = ajax.responseText;
						}
					}
					ajax.send("usernamecheck="+u);
				}
				console.log("checkUsername function end");	
			}
		</script>
		</body>
	</html>

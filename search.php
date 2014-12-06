<?php
include_once './includes/check_login_status.php';
//if user not logged in, redirect to index page
if($user_ok == false){
	header("location: index.php");
	exit();
}

//search code
if(isset($_GET["query"])){
	//sanitize input data
	$query = htmlentities($_GET["query"]);
	$query = mysqli_real_escape_string($db_con,$query);
	$resultText = "";

	$sql = "SELECT user, fname, lname FROM members WHERE user LIKE '$query%'";
	$srch_query = mysqli_query($db_con, $sql);
	$srch_rows = mysqli_num_rows($srch_query);
	
	//if no result found display this
	if($srch_rows < 1){

		$resultText = "<p>No results found for the search query</p>";
	}

	//fetch result from db
	//$result = array();
	while($row = mysqli_fetch_array($srch_query, MYSQLI_ASSOC)){
		/*array_push($result,$row["user"]);
		array_push($result,$row["fname"]);
		array_push($result,$row["lname"]);

		print_r($result);*/
		$res_user = $row["user"];
		$res_name = $row["fname"]." ".$row["lname"];
		//$res_lname = ;
		print_r($row);

		//display results to user
		$resultText = '<p><h3><a href="member.php?u='.$res_user.'">'.$res_user.' ('.$res_name.')</a></h3></p>';
	}


}
?>
<!DOCTYPE html>
	<html>
		<head>
			<title>Search</title>
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
									<h1>Search Results</h1>
								</div>
								<?php echo $resultText ?>
							</div>
						</div>
					</div>
				<?php include_once "./templates/template_pageFooter.php";?>
			</div>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
			<script src="bs/js/bootstrap.min.js"></script>
			<script src="js/ajax.js"></script>
			<script src="js/OSC.js"></script>
		</body>
	</html>
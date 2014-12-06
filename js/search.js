function search(){
				//console.log("function signup start");
				var search_str = O("search_form");
				if (search_str == "") {
					 alert("Not all the fields were entered.");
				}
				else{
					var ajax = ajaxObj("POST", "search.php");
					ajax.onreadystatechange = function() {
						if (ajax.responseText != "success") {
							alert(ajax.responseText);
							//console.log("inside ajax responseText block");
							//console.log(ajax.responseText);
						}
						else{
							//console.log("after ajax responseText success");
							window.location = "search.php?u="+ajax.responseText;
							
						}
					
					}
					ajax.send("fname="+fname+"&lname="+lname+"&uname="+uname+"&email="+email+"&pass="+pass+"&sex="+sex)
				}
				console.log("function signup end");
			}
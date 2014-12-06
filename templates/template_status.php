<?php
$status_ui = "";
$statuslist = "";
if($isOwner == true){
    $status_ui = '<textarea id="statustext" class="form-control" rows="3" onkeyup="statusMax(this,250)" placeholder="Say something '.$u.'"></textarea>';
    $status_ui .= '<button id="statusBtn" class="btn btn-info pull-right" onclick="postToStatus(\'status_post\',\'a\',\''.$u.'\',\'statustext\')">Post</button>';
}
else if($isFriend == true && $log_username != $u){
    $status_ui = '<textarea id="statustext" class="form-control" rows="3" onkeyup="statusMax(this,250)" placeholder="Hi '.$log_username.', say something to '.$u.'"></textarea>';
    $status_ui .= '<button id="statusBtn" class="btn btn-info pull-right" onclick="postToStatus(\'status_post\',\'c\',\''.$u.'\',\'statustext\')">Post</button>';
}
?>
<?php
$sql = "SELECT * FROM status WHERE account_name ='$u' AND type='a' OR account_name ='$u' AND type='c' ORDER BY postdate DESC LIMIT 20";
$query = mysqli_query($db_con,$sql);
$statusnumrows = mysqli_num_rows($query);
while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
    $statusid = $row["id"];
    $account_name = $row["account_name"];
    $author = $row["author"];
    $postdate = $row["postdate"];
    $data = $row["data"];
    $data = nl2br($data);
    $data = str_replace("&amp;","&",$data);
    $data = stripslashes($data);
    $statusDeleteButton = "";
    if($author == $log_username || $account_name == $log_username){
        $statusDeleteButton = '<span id="sdb"_'.$statusid.'"><a href="#" onclick="return false;" onmousedown="deleteStatus(\''.$statusid.'\',\'status_'.$statusid.'\')" title="Delete this status and all its replies">Delete status</a></span> &nbsp; &nbsp;';
    }
    // Gather up any status replies
    $status_replies = "";
    $query_replies = mysqli_query($db_con,"SELECT * FROM status WHERE osid='$statusid' AND type='b' ORDER BY postdate ASC");
    $replynumrows = mysqli_num_rows($query_replies);
    if($replynumrows > 0){
        while($row2 = mysqli_fetch_array($query_replies, MYSQLI_ASSOC)){
            $statusreplyid = $row2["id"];
            $replyauthor = $row2["author"];
            $replydata = $row2["data"];
            $replydata = nl2br($replydata);
            $replypostdate = $row2["postdate"];
            $replydata = str_replace("&amp;","&",$replydata);
            $replydata = stripslashes($replydata);
            $replyDeleteButton = '';
            if($replyauthor == $log_username || $account_name == $log_username ){
                $replyDeleteButton = '<span id="srdb_'.$statusreplyid.'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''.$statusreplyid.'\',\'reply_'.$statusreplyid.'\');" title="DELETE THIS COMMENT">remove</a></span>';
            }
            $status_replies .= '<div id="reply_'.$statusreplyid.'"class ="reply_boxes"><div><b>Reply by <a href="member.php?u='.$replyauthor.'">'.$replyauthor.'</a>'.$replypostdate.':<b> '.$replyDeleteButton.'<br/>'.$replydata.'</div></div>';
        }
    }   
    $statuslist .= '<div id="status_'.$statusid.'" class="status_boxes"><div><b>Posted by <a href="user.php?u='.$author.'">'.$author.'</a> '.$postdate.':</b> '.$statusDeleteButton.' <br />'.$data.'</div>'.$status_replies.'</div>';
    if($isFriend == true || $log_username == $u){
       $statuslist .= '<textarea id="replytext_'.$statusid.'" class="replytext" onkeyup="statusMax(this,250)" placeholder="write a comment here"></textarea><button class="btn btn-xs btn-primary" id="replyBtn_'.$statusid.'" onclick="replyToStatus('.$statusid.',\''.$u.'\',\'replytext_'.$statusid.'\',this)">Reply</button>';
    }
}
?>
<script>
    function postToStatus(action,type,user,ta) {
        var data = O(ta).value;
        if (data == "") {
            alert("Type something first genius");
            return false;
        }
        O("statusBtn").disabled = true;
        var ajax = ajaxObj("POST", "php_parsers/status_system.php");
        ajax.onreadystatechange = function(){
            if (ajaxReturn(ajax) == true) {
                var datArray = ajax.responseText.split("|");
                if (datArray[0] == "post_ok") {
                    var sid = datArray[1];
                    data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
                    var currentHTML = O("statusarea").innerHTML;
                    O("statusarea").innerHTML = '<div id="status_'+sid+'" class="status_boxes"><div><b>Posted by you just now:</b> <span id="sdb_'+sid+'"><a href="#" onclick="return false;" onmousedown="deleteStatus(\''+sid+'\',\'status_'+sid+'\');" title="DELETE THIS STATUS AND ITS REPLIES">delete status</a></span><br />'+data+'</div></div><textarea id="replytext_'+sid+'" class="replytext" onkeyup="statusMax(this,250)" placeholder="write a comment here"></textarea><button id="replyBtn_'+sid+'" onclick="replyToStatus('+sid+',\'<?php echo $u; ?>\',\'replytext_'+sid+'\',this)">Reply</button>'+currentHTML;
                    O("statusBtn").disabled = false;
                    O(ta).value = "";

                    //show statusarea div
                    var statbox = O("statusarea");
                    statbox.style.display = "block";
                }
                else{
                    alert(ajax.ResponseText);
                }
            }
        }
        ajax.send("action="+action+"&type="+type+"&user="+user+"&data="+data);
    }
    
    function replyToStatus(sid,user,ta,btn) {
        var data = O(ta).value;
        if (data == "") {
            alert("Type something first genius");
            return false;
        }
        O("replyBtn_"+sid).disabled = true;
        var ajax = ajaxObj("POST", "php_parsers/status_system.php");
        ajax.onreadystatechange = function(){
            if(ajaxReturn(ajax) == true) {
                var datArray = ajax.responseText.split("|");
                if(datArray[0] == "reply_ok"){
                    var rid = datArray[1];
                    data = data.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\n/g,"<br />").replace(/\r/g,"<br />");
                    O("status_"+sid).innerHTML += '<div id="reply_'+rid+'" class="reply_boxes"><div><b>Reply by you just now:</b><span id="srdb_'+rid+'"><a href="#" onclick="return false;" onmousedown="deleteReply(\''+rid+'\',\'reply_'+rid+'\');" title="DELETE THIS COMMENT">remove</a></span><br />'+data+'</div></div>';
                    O("replyBtn_"+sid).disabled = false;
                    O(ta).value = "";
                }
                else{
                    alert(ajax.ResponseText);
                }
            }
        }
        ajax.send("action=status_reply&sid="+sid+"&user="+user+"&data="+data);
    }

    function deleteStatus(statusid,statusbox) {
        var conf = confirm("Press OK to confirm deletion of this status and all its replies");
        if (conf != true) {
            return false;
        }
        var ajax = ajaxObj("POST","php_parsers/status_system.php");
        ajax.onreadystatechange = function(){
            if (ajaxReturn(ajax) == true) {
                if (ajax.responseText == "delete_ok") {
                    O(statusbox).style.display = 'none';
                    O("replytext_"+statusid).style.display = 'none';
                    O("replyBtn_"+statusid).style.display = 'none';

                    //hide statusarea div
                    var statbox = O("statusarea");
                    statbox.style.display = "none";
                }
                else {
                    alert(ajax.responseText);
                }
            }
        }
        ajax.send("action=delete_status&statusid="+statusid);
    }
    
    function deleteReply(replyid,replybox) {
        var conf = confirm("Press OK to confirm deletion of this reply");
        if(conf != true){
            return false;
        }
        var ajax = ajaxObj("POST", "php_parsers/status_system.php");
        ajax.onreadystatechange = function() {
            if(ajaxReturn(ajax) == true) {
                if(ajax.responseText == "delete_ok"){
                    O(replybox).style.display = 'none';
                    }
                    else {
                        alert(ajax.responseText);
                    }
            }
        }
        ajax.send("action=delete_reply&replyid="+replyid);

    }
    
    function statusMax(field, maxlimit) {
        if (field.value.length > maxlimit) {
            alert(maxlimit+" maximum character limit reached");
            field.value = field.value.substring(0, maxlimit);
        }
    }
</script>
<script>
function showStatBox(){ 
	var container = document.getElementById("statusarea");
	var con = container.childNodes.length;
    var dcon = container.childNodes;
	//var icon = container.childNodes.item(0);
    var elems = [];
    for(var i=0; i<con; i++){
        console.log(dcon[i].nodeType);
        //if nodeType is not text, add to array
        if(dcon[i].nodeType != 3){
            elems.push(dcon[i]);
        }
    }
    console.log("Length: " +con+ " divs: " +dcon.length);
	if(dcon.length > 1){
		container.style.display = "block";
	}
	else{
		container.style.display = "none";
		
	}
}
</script>

<div id="statusui" class="col-md-8 well well-sm">
    <?php echo $status_ui; ?>
</div>
<div id="statusarea" class="col-md-8 well well-sm">
    <?php echo $statuslist; ?>
</div>
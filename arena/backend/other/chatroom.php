<?php 

if(isset($_GET['getMessage'])) {
getMessages();	
}
if(isset($_POST['postMessage'])){
	$message = $_POST['postMessage'];
	if (strlen($message) > 0){
	    if(strlen($message) < 500){
		  postMessage(strip_tags($message));
        }
	}
}
if (isset($_POST['chat'])){
    chatToggle($_POST['chat']);
}

function getMessages(){
	$date = date("Ymd");
	$fileName = __ROOT__."/public_html/frontend/chat/$date.txt";
	$myfile = fopen($fileName, "a+") or die("Error reading file");
	$text = fread($myfile,filesize($fileName));
	if ($text){
		echo strip_tags($text,"'<br>','<a>','<img>','<strong>'");
	}
	else{
		fwrite($myfile,"&#13;&#10 ");
	}
	fclose($myfile);

}

function postMessage($message){
	if (isset($_SESSION['loggedIn'])){
	    if(isset($_SESSION['other']['chatIcon'])){
	        $chatIcon = "<img src=\"frontend/design/images/chatIcons/" . $_SESSION['other']['chatIcon'] . "\"> ";
	    }
	    else{
	        $chatIcon = "";
	    }
		$date = date("Ymd");
		
		$fileName = __ROOT__."/public_html/frontend/chat/$date.txt";
		$myfile = fopen($fileName, "a+") or die("Error reading file");
		$time = date("H:i");
		if(isset($_SESSION['characterProperties']['name'])){
			$charName = $_SESSION['characterProperties']['name'];
		}
		else{
			$charName = "";
		}
		fwrite($myfile, $time . " - " . $chatIcon . "<a class=\"headerButtonLink\" href=\"index.php?page=view-character&username=" . $_SESSION['loggedIn'] ."\">" . $_SESSION['loggedIn'] . " (" . $charName . ")</a>: " . $message . "<br>");
		fclose($myfile);
	}
}

function chatToggle($on){
        global $conn;
        $username = $_SESSION['loggedIn'];
        $sql = "UPDATE users SET chatToggle=? WHERE username=?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, "is", $on,$username);
        mysqli_stmt_execute($stmt);
        $_SESSION['other']['chatToggle'] = $on;
}



?>
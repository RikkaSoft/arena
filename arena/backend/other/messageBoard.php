<?php 

if(isset($_GET['getMessage'])) {
getMessages();	
}
if(isset($_POST['postMessage'])){
	$message = $_POST['postMessage'];
	$message = str_replace("'","''",$message);
	postMessage($message);
}

	function getMessages(){
		global $conn;
		$sql = "SELECT * FROM tavern ORDER BY id DESC";
		$result = mysqli_query($conn, $sql);
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$post = $row['message'];
				$author = $row['author'];
				$date = $row['date'];
				$day = str_replace("-", "/",substr($date,0,-6));
				$time = str_replace("-", ":",substr($date,-5));
				
				
				echo " " . $day . " " . $time . " - " . $author . ": " . $post . "&#13;&#10";
				
			}
	}

	function postMessage($message){
		
		global $conn;
		$author = $_SESSION['loggedIn'];
		$date = date("Y-m-d-H-i-s");
		$sql = "INSERT INTO tavern (author,message,date) VALUES ('$author','$message','$date')";
		$result = mysqli_query($conn,$sql) or die("Error: ".mysqli_error($conn));
		header('Location: index.php?page=tavern');

	}

?>
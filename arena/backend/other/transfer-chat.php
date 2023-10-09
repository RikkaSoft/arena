<?php
	$date = date("Ymd");
	$dateTomorrow = $date+1;
	$fileName = __ROOT__."/public_html/frontend/chat/$date.txt";
	$fileNameTomorrow = __ROOT__."/public_html/frontend/chat/$dateTomorrow.txt";

	$file = file_get_contents($fileName);
	$filesplit = explode("<br>", $file);
	
	$myfile = fopen($fileNameTomorrow, "a+") or die("Error reading file");
	
	for ($i = max(0, count($filesplit)-20); $i < count($filesplit); $i++) {
	 fwrite($myfile,$filesplit[$i] . "<br>");
	}
	
	fclose($myfile);

?>
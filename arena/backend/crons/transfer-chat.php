<?php
	$date = date("Ymd");
	$dateTomorrow = $date+1;
	$fileName = "/var/www/html/arena/public_html/frontend/chat/$date.txt";
	$fileNameTomorrow = "/var/www/html/arena/public_html/frontend/chat/$dateTomorrow.txt";

	$file = file_get_contents($fileName);
	$filesplit = explode("<br>", $file);
	#echo "Count : " . count($filesplit);
	touch($fileNameTomorrow);
	chown($fileNameTomorrow, "www-data" );
	$myfile = fopen($fileNameTomorrow, "a+") or die("Error reading file");
	
	for ($i = max(0, count($filesplit)-50); $i < count($filesplit); $i++) {
	 #echo "\n";
	 #echo $filesplit[$i];
	 fwrite($myfile,$filesplit[$i] . "<br>");
	}
	
	fclose($myfile);

?>
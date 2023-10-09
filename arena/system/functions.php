<?php
	global $page;
	foreach($_GET as $key => $var){
		/*if (strpos($_GET[$key], '(') !== false) {
			echo "You've clicked on a bad link.. =(<br> Don't worry, nothing bad happened, It's been stopped";
			exit;
		}
		if (strpos($_GET[$key], ')') !== false) {
			echo "You've clicked on a bad link.. =(<br> Don't worry, nothing bad happened, It's been stopped";
			exit;
		}*/
		$_GET[$key] = htmlentities (strip_tags($_GET[$key]));
	}
	foreach($_POST as $key => $var){

		$_POST[$key] = htmlentities (strip_tags($_POST[$key]));
	}
	function getPage()
	{
		if(isset($_GET['page']))
		{
			$page = str_replace("..\\", "", $_GET['page']);
			{
			require_once("frontend/pages/$page.php");
			}
		}
		
		elseif(isset($_GET['bpage']))
		{
			$bpage = str_replace("..\\", "", $_GET['bpage']);
			require_once(__ROOT__."/backend/accounts/$bpage.php");
		}
		
		elseif(isset($_GET['cpage']))
		{
			$cpage = str_replace("..\\", "", $_GET['cpage']);
			if($cpage == "get-character" || $cpage == "get-character-large"){
				require_once(__ROOT__."/backend/character/getCharacterData.php");
			}
			require_once(__ROOT__."/backend/character/$cpage.php");
		}
		elseif(isset($_GET['fpage']))
		{
			$fpage = str_replace("..\\", "", $_GET['fpage']);
			require_once(__ROOT__."/backend/fighting/$fpage.php");
		}
		elseif(isset($_GET['opage']))
		{
			$opage = str_replace("..\\", "", $_GET['opage']);
			require_once(__ROOT__."/backend/other/$opage.php");
			
		}
		elseif(isset($_GET['apage']))
		{
			$apage = str_replace("..\\", "", $_GET['apage']);
			require_once(__ROOT__."/backend/adventure/$apage.php");
		}
		elseif(isset($_GET['tpage']))
		{
			$tpage = str_replace("..\\", "", $_GET['tpage']);
			require_once(__ROOT__."/backend/tournament/$tpage.php");
		}
		elseif(isset($_GET['gpage']))
		{
			$gpage = str_replace("..\\", "", $_GET['gpage']);
			require_once(__ROOT__."/backend/guild/$gpage.php");
		}
		elseif(isset($_GET['adpage']))
		{
			$adpage = str_replace("..\\", "", $_GET['adpage']);
			require_once(__ROOT__."/backend/admin/$adpage.php");
		}
		elseif(isset($_GET['crpage'])){
			$crpage = str_replace("..\\", "", $_GET['crpage']);
			require_once(__ROOT__."/backend/crafting/$crpage.php");
		}
		elseif(isset($_GET['qpage'])){
			$qpage = str_replace("..\\", "", $_GET['qpage']);
			require_once(__ROOT__."/backend/quests/$qpage.php");
		}
		else
		{
    			if (isset($_SESSION['loggedIn'])){
    				$_GET['page'] = "news";
    				require_once("frontend/pages/news.php");
    			}
    			else{
    				$_GET['page'] = "welcome";
    				require_once("frontend/pages/welcome.php");
    			}
            #}
		}

	}
?>			

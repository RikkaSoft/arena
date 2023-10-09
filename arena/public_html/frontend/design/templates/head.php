<head>
    <link rel="stylesheet" type="text/css" href="frontend/design/css/bootstrap2.css">
	<link rel="stylesheet" type="text/css" href="frontend/design/css/default10.css">
	<script type="text/javascript" src="frontend/design/js/jquery.min.js"></script>
	<script type="text/javascript" src="frontend/design/js/jquery.tablesorter.min.js"></script> 
	<script type="text/javascript" src="frontend/design/js/jquery-ui.min.js"></script> 
	<meta charset="utf-8">
	<link rel="icon" type="image/png" href="frontend/design/images/other/favicon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>The Arena of Rikka</title>
	<script>
		function loadMainPage(page){
			var target = 'index.php?page=' + page;
			$('#mainPage').load(target + '&nonUI');
			var pageName = 'Arena.rikka.se - ' + page;
			history.pushState({
			    id: pageName
			}, pageName, target);
			if ($(window).width() < 768) {
				toggleMenu();
			}
		}
		$( document ).ready(function() {
			$('#logoutButton').click(function(){
				$('#mainPage').load('index.php?page=logout&nonUI',function(){
					updateChar();
				});
			});
		});

	</script>
</head>
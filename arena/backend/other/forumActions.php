<?php 
	require_once(__ROOT__."/system/details.php");
	function getForumCategories(){
		global $conn;
		$sql = "SELECT title, data FROM tavern WHERE topic_type='forum_category'";
		$results = mysqli_query($conn, $sql);
		echo "<div id=\"forumTopMenu\"><a class=\"lessFormatLinks\" href=\"index.php?page=tavern\"><h2>Tavern</h2></a></div><br><br>";
		echo "<div id=\"forumTableHolder\">
			<table id=\"forumTable\" style=\"width:100%\">
				<tbody>
				<tr id=\"forumTableHeader\">
						<td colspan=\"4\">
							Category
						</td>
						<td colspan=\"1\">
							Topics
						</td>
						<td colspan=\"1\">
							Posts
						</td>
						<td colspan=\"2\">
							Last Post
						</td>
					</tr>";
		
		while($row = mysqli_fetch_assoc($results)){
		
		$category = $row['title'];
		$information = $row['data'];
		
			echo "<tr class=\"forumTablePost\">
					<td colspan=\"4\"><a class=\"forumTopicText\" href=\"index.php?page=tavern&getPosts=" . $category . "\">" . $category ."</a><br><p class=\"forumInformationText\">" . $information . "</p></td>
					<td colspan=\"1\">";
							$qPost = "forum_post_" . $category;
							$sql = "SELECT id FROM tavern WHERE topic_type=?";
							$stmt = mysqli_prepare($conn,$sql);
							mysqli_stmt_bind_param($stmt, "s", $qPost);
							mysqli_stmt_execute($stmt);
							$topics = $stmt->get_result();
							$count = mysqli_num_rows($topics);
							echo $count . 
						"</td>
						<td colspan=\"1\">";
							$qReply = "forum_reply_" . $category;
							$sql = "SELECT id FROM tavern WHERE topic_type=? OR topic_type=?";
							$stmt = mysqli_prepare($conn,$sql);
							mysqli_stmt_bind_param($stmt, "ss", $qReply,$qPost);
							mysqli_stmt_execute($stmt);
							$topics = $stmt->get_result();
							$count = mysqli_num_rows($topics);
							echo $count .
						"</td>
						<td colspan=\"2\">";
							$sql = "SELECT title, author, date, lastPost FROM tavern WHERE topic_type=? ORDER BY lastUpdated DESC LIMIT 1";
							$stmt = mysqli_prepare($conn,$sql);
							mysqli_stmt_bind_param($stmt, "s", $qPost);
							mysqli_stmt_execute($stmt);
							$lastPost = $stmt->get_result();
							$row = mysqli_fetch_assoc($lastPost);
							
							$lastPost = $row['lastPost'];
								#if ($lastPost !== ""){
									$sql = "SELECT id,replyTo, title, author, date FROM tavern WHERE id = ?";
									$stmt = mysqli_prepare($conn,$sql);
									mysqli_stmt_bind_param($stmt, "i", $lastPost);
									mysqli_stmt_execute($stmt);
									$lastPost = $stmt->get_result();
									$row = mysqli_fetch_assoc($lastPost);
									$date = $row['date'];
									if(isset($row['replyTo'])){
										$threadId = $row['replyTo'];
									}
									else{
										$threadId = $row['id'];
									}
									
									
									if ($row['title'] != ""){
										if (strlen($row['title']) > 50){
											echo "<a class=\"forumTopicText\" href=\"index.php?page=tavern&getPosts=" . $category . "&getThread=" . $threadId . "\">" . substr($row['title'],0,50) . "...</a>";
										}
										else{
											echo "<a class=\"forumTopicText\" href=\"index.php?page=tavern&getPosts=" . $category . "&getThread=" . $threadId . "\">" . $row['title'] . "</a>";
										}
										echo "<p class=\"postAuthor\">
											By: <a class=\"forumAuthor\" href=\"index.php?page=view-character&username=" . $row['author'] . "\">" . $row['author'] . "</a> > " . $date . 
										"</p>";
								}
							#}
						echo "</td>
					</tr>";
		}
		echo "</tbody>
			</table>
		</div>";
	}


	function getPosts($postType){
		global $conn;
		$sql = "SELECT id FROM tavern WHERE topic_type=?";
		$stmt = mysqli_prepare($conn,$sql);
		$truePostType = "forum_post_" . $postType;
		mysqli_stmt_bind_param($stmt, "s", $truePostType);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$postCount = mysqli_num_rows($result);
		
		if (!isset($_GET['getPage'])){
			$pageNumber = 0;
			$sql = "SELECT title,author,views,replies,lastPost,id,date FROM tavern WHERE topic_type=? ORDER BY lastUpdated desc LIMIT ?,10";
		}
		else{
			$pageNumber = ($_GET['getPage']-1) * 10;
			$sql = "SELECT title,author,views,replies,lastPost,id,date FROM tavern WHERE topic_type=? ORDER BY lastUpdated desc LIMIT ?,10";
		}
		$stmt = mysqli_prepare($conn,$sql);
		$truePostType = "forum_post_" . $postType;
		mysqli_stmt_bind_param($stmt, "si", $truePostType,$pageNumber);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		
		
		
		echo "<div id=\"forumTopMenu\"><a class=\"lessFormatLinks\" href=\"index.php?page=tavern\"><h2>Tavern</a> > <a class=\"lessFormatLinks\" href=\"index.php?page=tavern&getPosts=" . $postType . "\">". $postType . "</h2></a></div>
		<br><div id=\"newPostDiv\">
			<button class='button' type=\"button\"onclick=\"getElementById('newPostDiv').style.display = 'none'; showNewPost('". $postType . "');\"> New Topic</button>
		</div>";
		echo "<div id=\"showPostDiv\">
							</div>";
		
		echo "<div id=\"postPages\">"; 
		$i = 1;
		echo "<ul id=\"postPagesList\">";
		while ($postCount > 0){
			echo "<a href=\"index.php?page=tavern&getPosts=" . $postType . "&getPage=" . $i . "\"><li class=\"postPagesButton\">" . $i . "</li></a>";
			$postCount = $postCount-10;
			$i++;
		} 
		echo "</ul>";
		echo "</div>";
		
		echo "<div id=\"forumTableHolder\">
			<table id=\"forumTable\" style=\"width:100%\">
				<tbody>
				<tr id=\"forumTableHeader\">
						<td colspan=\"4\">
							Topic
						</td>
						<td colspan=\"1\">
							Replies
						</td>
						<td colspan=\"1\">
							Views
						</td>
						<td colspan=\"2\">
							Last Post
						</td>
					</tr>";
		
		while($row = mysqli_fetch_assoc($result)){
		
		$postTitle = 	$row['title'];
		$postReplies = 	$row['replies'];
		$postViews = 	$row['views'];
		$postLastPost = $row['lastPost'];
		$postId =		$row['id'];
			$date = $row['date'];

			echo "<tr class=\"forumTablePost\">
					<td colspan=\"4\"><a class=\"forumTopicText\" href=\"index.php?page=tavern&getPosts=" . $postType . "&" . "getThread=" . $postId . "\">" . strip_tags($postTitle) ."</a>
					<p class=\"postAuthor\">
						By: <a class=\"forumAuthor\" href=\"index.php?page=view-character&username=" . $row['author'] . "\">" . $row['author'] . "</a> > " . $date . 
					"</p>
					</td>
					<td colspan=\"1\">";
							echo $postReplies . 
						"</td>
						<td colspan=\"1\">";
							echo $postViews .
						"</td>
						<td colspan=\"2\">";
							$sql = "SELECT data,author, date FROM tavern WHERE id=?";
							$stmt = mysqli_prepare($conn,$sql);
							mysqli_stmt_bind_param($stmt, "i", $postLastPost);
							mysqli_stmt_execute($stmt);
							$lastPost = $stmt->get_result();
							$row = mysqli_fetch_assoc($lastPost);
							$date = $row['date'];
							if (strlen($row['data']) > 30){
								echo strip_tags(substr($row['data'],0,30)) . "...";
							}
							else{
								echo strip_tags($row['data']);
							}
							echo "<p class=\"postAuthor\">
								By: <a class=\"forumAuthor\" href=\"index.php?page=view-character&username=" . $row['author'] . "\">" . $row['author'] . "</a> > " . $date . 
							"</p>";
						echo "</td>
					</tr>";
		}
		echo "</tbody>
			</table>
			
		</div>";
		echo "<br><br>";
		
		echo "<script>
			function showNewPost(postCategory){
				$('#showPostDiv').load('index.php?opage=forumActions&nonUI&showNewPost=' + postCategory);
			}
			</script>";
	}

	function getThread($category,$threadId){
		global $conn;
		$sql = "SELECT title, data, author,date FROM tavern WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $threadId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
			$date = $row['date'];
			$threadTitle = $row['title'];
			$postIdCounter = 1;
			
			if (strlen($threadTitle) > 30){
				$threadName = substr($threadTitle,0,30) . "...";
			}
			else{
				$threadName = $threadTitle;
			}
		
		
		
		echo "<div id=\"forumTopMenu\"><a class=\"lessFormatLinks\" href=\"index.php?page=tavern\">
		<h2>Tavern</a> > <a class=\"lessFormatLinks\" href=\"index.php?page=tavern&getPosts=" . $category . "\">". $category . "</a>" . 
		" > <a class=\"lessFormatLinks\" href=\"index.php?page=tavern&getPosts=" . $category . "&getThread=" . $threadId . "\">" . $threadName . "</a></h2></div><br><br>";
		
		if ($_SESSION['loggedIn'] == $row['author']){
			$buttons = "<a href=\"javascript:editPost(" . $threadId . ")\"><img src=\"frontend/design/images/buttons/edit.png\"></a>";
			$buttons .= "<a href=\"javascript:removePost(" . $threadId . ",'" . $category . "')\"><img src=\"frontend/design/images/buttons/deletePost.png\"></a>";
		}
		else{
			$buttons = "";
		}
		
		echo "<div id=\"threadContainer\">";
		echo "<div id=\"postContainer\">";
		echo "<div id=\"postTitle\">" . strip_tags($threadTitle) . "<div id=\"postIdCounter\">" . $buttons . " - " . $postIdCounter . ". " . "</div><br>
		<p class=\"postAuthor\">
		By: <a class=\"forumAuthor\" href=\"index.php?page=view-character&username=" . $row['author'] . "\">" . $row['author'] . "</a> > " . $date . 
		"</p>
		</div>";
		echo "<div class=\"postBody\" id=\"postBody" . $threadId . "\">" . strip_tags(nl2br($row['data']),'<br>,<ul>,<li>') . "</div>";
		echo "</div>";
		echo "<div id=\"saveEdit" . $threadId . "\"> </div>";
		
		
		$sql = "SELECT id,data, author, date FROM tavern WHERE replyTo=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $threadId);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		while ($row = mysqli_fetch_assoc($result)){
			$date = $row['date'];
			$postIdCounter++;
		
		if ($_SESSION['loggedIn'] == $row['author']){
			$buttons = "<a href=\"javascript:editPost(" . $row['id'] . ")\"><img src=\"frontend/design/images/buttons/edit.png\"></a>";
			$buttons .= "<a href=\"javascript:removePost(" . $row['id'] . ",'" . $category . "'," . $threadId . ")\"><img src=\"frontend/design/images/buttons/deletePost.png\"></a>";
		}
		else{
			$buttons = "";
		}
		echo "<div id=\"postContainer\">";
		echo "<div id=\"postTitle\">" . "Re: " . strip_tags($threadTitle) . "<div id=\"postIdCounter\">" . $buttons . " - " . $postIdCounter . "</div><br>
		<p class=\"postAuthor\">
		By: <a class=\"forumAuthor\" href=\"index.php?page=view-character&username=" . $row['author'] . "\">" . $row['author'] . "</a> > " . $date . 
		"</p>
		</div>";
		echo "<div class=\"postBody\" id=\"postBody" . $row['id'] . "\">" . strip_tags(nl2br($row['data']),'<br>') . "</div>";
		echo "</div>";
		echo "<div id=\"saveEdit" . $row['id'] . "\"> </div>";
		}
		echo "</div>";
		echo "<br><br>";
		echo "<div id='replyWrapper'>";
		echo "<div id='postTitle' style='font-size:22px;'>Your Reply</div>";
		echo "<form action='' method='post' onsubmit='postNewReply();return false;' >";
		echo "<input type=\"hidden\" name=\"threadId\" value=\"" . $threadId . "\" id='threadId'>";
		echo "<input type=\"hidden\" name=\"category\" value=\"" . $category . "\" id='category'>";
		echo "<textarea id=\"postReplyTextBox\" name=\"replyText\" placeholder=\"Type your reply here...\"></textarea>";
		echo "<input type=\"submit\" value=\"Post Reply\">";
		echo "</form>";
		echo "</div>";
		echo "<script>
			function postNewReply(){
				$('#mainPage').load('index.php?opage=forumActions&nonUI',{
					'threadId': $('#threadId').val(),
					'category': $('#category').val(),
					'replyText': $('#postReplyTextBox').val()
				});
			}
		</script>";
		
		$sql = "UPDATE tavern SET views=views+1 WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $threadId);
		mysqli_stmt_execute($stmt);	
		
	}

	function replyToThread(){
		global $conn;
		$type = 			"forum_reply_" . $_POST['category'];
		$data = 			$_POST['replyText'];
		if (!isset($_SESSION['loggedIn'])){
			echo "You don't seem to be logged in";
			exit;
		}
		$author = 			$_SESSION['loggedIn'];
		$date = date("Y/m/d - H:i");
		$replyTo = 			$_POST['threadId'];
		$sql = "SELECT title FROM tavern WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $replyTo);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		
		$replyTitle = "Re: " . $row['title'];
		
		$sql = "INSERT INTO tavern (topic_type, title, data, author, date, replyTo) VALUES (?,?,?,?,?,?)";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "sssssi", $type,$replyTitle, $data,$author,$date,$replyTo);
		mysqli_stmt_execute($stmt);
		$thisId = mysqli_insert_id($conn);
		
		$updateDate = date("YmdHi");
		
		$sql = "UPDATE tavern SET replies=replies+1, lastPost=?,lastUpdated=?  WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "isi", $thisId,$updateDate,$replyTo);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		#mysqli_query($conn, $sql);
		
		getThread($_POST['category'],$_POST['threadId']);
		#header("Location: index.php?page=tavern&getPosts=" . $_POST['category'] . "&getThread=" . $_POST['threadId'] . ""); 
	}
	
	function showNewPost($category){
		
		echo "<h2>Create a new topic</h2>";
		echo "<form action=\"\" method='post' onsubmit='postNewPost();return false;' >";
		echo "<input type=\"hidden\" name=\"category\" value=\"" . $category . "\" id='currCat'>";
		echo "The Title of your topic (max 256 characters)<br>";
		echo "<input type=\"text\" name=\"newPostTitle\" required id='newPostTitle' class='newPostTitle'><br>";
		echo "The Content of your post (max 4096 characters)<br>";
		echo "<textarea id=\"postReplyTextBox\" name=\"newPostData\" placeholder=\"The content of your topic...\" required ></textarea><br><br>";
		echo "<input type=\"submit\" value=\"Post new topic\">";
		echo "</form>";
		echo "<script>
		
			function postNewPost(){
				$('#mainPage').load('index.php?opage=forumActions&nonUI', {
					'category': $('#currCat').val(),
					'newPostTitle': $('#newPostTitle').val(),
					'newPostData': $('#postReplyTextBox').val()
				});
			}
			
		</script>";
		
	}

	function newPost(){
		global $conn;

		$type = 			"forum_post_" . $_POST['category'];
		$title = 			$_POST['newPostTitle'];
		$data = 			$_POST['newPostData'];
		$author = 			$_SESSION['loggedIn'];
		$date = 			date("Y/m/d - H:i");
		
		$updateDate = date("YmdHi");
		
		$sql = "INSERT INTO tavern (topic_type, title, data, author, date, lastUpdated) VALUES (?,?,?,?,?,?)";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "ssssss", $type,$title,$data,$author,$date,$updateDate);
		mysqli_stmt_execute($stmt);
		$thisId = mysqli_insert_id($conn);
		$sql = "UPDATE tavern SET lastPost=? WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "ii", $thisId,$thisId);
		mysqli_stmt_execute($stmt);
		
		getPosts($_POST['category']);
		#header("Location: index.php?page=tavern&getPosts=" . $_POST['category'] ."");
	}
	
	function removePost($id){
		global $conn;
		$username = $_SESSION['loggedIn'];
		$sql = "DELETE FROM tavern WHERE id=? AND author=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "is", $id,$username);
		mysqli_stmt_execute($stmt);
		
	}
	
	function updatePost(){
		global $conn;
		$username = $_SESSION['loggedIn'];
		$id = $_POST['id'];
		$newText = $_POST['newText'];
		$sql = "SELECT author FROM tavern WHERE id=?";
		$stmt = mysqli_prepare($conn,$sql);
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
		$result = $stmt->get_result();
		$row = mysqli_fetch_assoc($result);
		
		if ($row['author'] == $username){
			$search = array("<div>","</div>");
			$newText = str_replace($search, "", $newText);
			$newText .= "<br><br>Edited: " . date("Y/m/d - H:i");
			$sql = "UPDATE tavern SET data=? WHERE id=?";
			$stmt = mysqli_prepare($conn,$sql);
			mysqli_stmt_bind_param($stmt, "si", $newText, $id);
			mysqli_stmt_execute($stmt);
		}
	}
	
	if (isset($_POST['newText'])){
		updatePost();
	}
	
	if(isset($_GET['removePost'])){
		removePost($_GET['removePost']);
	}
	
	if(isset($_POST['newPostTitle'])){
		newPost();
	}
	
	if(isset($_POST['replyText'])){
		replyToThread();
	}
	
	if(isset($_GET['showNewPost'])){
		showNewPost($_GET['showNewPost']);
	}
	
	elseif(isset($_GET['getPosts']) && (isset($_GET['getThread']))){
		getThread($_GET['getPosts'],$_GET['getThread']);
	}
	
	elseif (isset($_GET['getPosts'])){
		if($_GET['getPosts'] == "getforumCategories"){
			getForumCategories();
		}
		else{
			getPosts($_GET['getPosts']);
		}
	}
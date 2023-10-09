<script>
	$(document).ready(
		function () {
			var category = '<?php 
				if(isset($_GET['removePost']) && (isset($_GET['getPosts'])) && (isset($_GET['getThread']))){
					echo "removePost=" . $_GET['removePost'] . "&" . "getPosts=" . $_GET['getPosts'] . "&" . "getThread=" . $_GET['getThread'];
				}
				elseif(isset($_GET['removePost']) && (isset($_GET['getPosts']))){
					echo "removePost=" . $_GET['removePost'] . "&" . "getPosts=" . $_GET['getPosts'];
				}
				elseif(isset($_GET['getPosts']) && (isset($_GET['getThread']))){
					echo "getPosts=" . $_GET['getPosts'] . "&" . "getThread=" . $_GET['getThread'];
				} 
				elseif(isset($_GET['getPosts']) && isset($_GET['getPage'])){
					echo "getPosts=" . $_GET['getPosts'] . "&getPage=" . $_GET['getPage'];
				}
				elseif(isset($_GET['getPosts'])){
					echo "getPosts=" . $_GET['getPosts'];
				}
				else {
					echo "getPosts=getforumCategories";
				} 
				?>';
			$('#forumTables').load('index.php?opage=forumActions&nonUI&' + category);
			
			

	});
	 
	function removePost(id,category,thread){
	
			if(! thread){
				var deleteConfirm = confirm("Do you really want to remove your topic?\nAll replies to this topic will also be removed");
					if (deleteConfirm == true){
						window.location.replace('index.php?page=tavern&removePost=' + id + '&getPosts=' + category);
					}
			}
			else{
				var deleteConfirm = confirm("Do you really want to remove your reply?");
					if (deleteConfirm == true){
						window.location.replace('index.php?page=tavern&removePost=' + id + '&getPosts=' + category + '&getThread=' + thread);
					}
			}
	}
	
	function editPost(id){
		document.getElementById('postBody' + id + '').contentEditable = true;
		document.getElementById('postBody' + id + '').style.backgroundColor="#b45919";
		document.getElementById('saveEdit' + id + '').innerHTML = "<button onclick=\"saveEdit('" + id + "')\">Save</button><br><br><br>";
	}
	function saveEdit(id){
		var newText = document.getElementById('postBody' + id + '').innerHTML;
		$.post('index.php?opage=forumActions&nonUI',
		  {
		  	id: id,
			newText: newText
		   }, function(){
		  	document.getElementById('saveEdit' + id + '').innerHTML = "";
		  	document.getElementById('postBody' + id + '').contentEditable = false;
			document.getElementById('postBody' + id + '').style.backgroundColor="#8b4513";
		  });
	}
	
</script>
<div id="forumTables">

</div>

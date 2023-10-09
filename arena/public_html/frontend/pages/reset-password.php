<?php 

if (isset($_GET['passwordResetId']) && isset($_GET['email'])){
	$passwordResetId = $_GET['passwordResetId'];
	$email = $_GET['email'];
?>
<br><br>
<form role="register" action="index.php?bpage=password-reset&nonUI" method="post" style='width:200px;margin: 0px auto; text-align:center;'>
		New password <br>(min 8 characters) <input type="password" class="form-control" id="password" name="password" required pattern=".{8,}"/>
		<input type='string' class="form-control" id="passwordResetId" name="passwordResetId" value="<?php echo $passwordResetId; ?>" hidden style='display:none;'>
		<input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>"   hidden style='display:none;'>
		<br>
	<button type="submit" class="btn btn-default">
		Set Password
	</button>
</form>

<?php
	
}
else{
	
?>
<br><br>
<form role="register" action="index.php?bpage=password-reset-mail" method="post" style='width:400px;margin: 0px auto; text-align:center;'>
		Enter your email <input type="email" class="form-control" id="email" name="email" required/>
	<br>
	<button type="submit" class="btn btn-default">
		Send me a mail with a password recovery link!
	</button>
</form>
<?php
}
?>
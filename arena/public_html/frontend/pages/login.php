		<div style='text-align:center;'>
				<h2>Login</h2> 
				<br>
				
				<?php 
					if(isset($_GET['loginFail'])){
						$reason = $_GET['loginFail'];
						if ($reason == "wrongPass")
						{
							echo "<div class=\"alert alert-danger\">
							  <strong>Wrong username or password, try again</strong>
							</div>";
						} 
						elseif
						($reason == "noUser"){
							echo "<div class=\"alert alert-danger\">
							  <strong>User doesn't exist</strong>
							</div>";
						}
					}
					if(isset($_SESSION['registerSuccess']))
					{
						echo "<div class=\"alert alert-success\">
						  <strong>User " . $_SESSION['registerSuccess'] . " Created, please log in to continue</strong>
						</div>";
						unset($_SESSION['registerSuccess']);
					}
					
					
					if (isset($_SESSION['passwordChange'])){
						echo "<div class=\"alert alert-success\">
						  <strong>" . $_SESSION['passwordChange'] . "</strong>
						</div>";
						unset($_SESSION['passwordChange']);
					}
				?>
			<div style='margin: 0px auto;width:250px;'>
    			<form role="login" action="index.php?bpage=login-handling&nonUI" method="post">
    						<label for="username">Username </label>
    						<br>
    						<input type="username" class="form-control" id="username" name="username" required />
    						<br>
    						<label for="password">Password</label>
    						<br>
    						<input type="password" class="form-control" id="password" name="password" required/>
    				<br>
    				<button type="submit" class="button loginButton">
    					Login
    				</button>
    			</form>
    			<br><br>
    			<a href="index.php?page=register">No account? Register here!</a>
    			<br><br>
    			<a href="index.php?page=reset-password">Forgotten your password?</a>
			</div>
		</div>
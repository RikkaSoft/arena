						<div style='text-align:center;'>
							<h2>Register a new Account</h2>
							To register please fill in this simple registration form. Please provide a valid E-mail address.<br>
							Allowed characters for your username: a-z, A-Z, 0-9
							<br>
							<br>
							<?php 
							if (isset($_SESSION['registerFail']))
							{
								echo "<font color='red'>" . $_SESSION['registerFail'] . "</font></br>";
								unset($_SESSION['registerFail']);
							} ?>
						<div class="inputContainer" style='margin: 0px auto;width:250px;'>
							<form role="register" action="index.php?bpage=registration-handling&nonUI" method="post">
								<div class="form-group">
									<label for="username">Username:</label>
									<br>
									<input type="username" class="form-control" id="username" name="username" required pattern=".{3,16}"   required title="3 characters minimum, 16 characters max"/>
								</div>
								<br>
								<div class="form-group">
									<label for="password">Password:</label>
									<br>
									<input type="password" class="form-control" id="password" name="password" required pattern=".{8,}"   required title="8 characters minimum"/>
								</div>
								<br>
								<div class="form-group">
									<label for="email">E-mail Address:</label>
									<br>
									<input type="email" class="form-control" id="email" name="email" required/>
								</div>
								<br>
								<button type="submit" class="button loginButton">
									Register
								</button>
							</form>
						</div>
					</div>
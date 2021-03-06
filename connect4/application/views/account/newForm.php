<?php include('inc/header.php'); ?>
	<div class="content">
			<script src="http://code.jquery.com/jquery-latest.js"></script>
			<script>
				function checkPassword() {
					var p1 = $("#pass1"); 
					var p2 = $("#pass2");
					
					if (p1.val() == p2.val()) {
						p1.get(0).setCustomValidity("");  // All is well, clear error message
						return true;
					}	
					else	 {
						p1.get(0).setCustomValidity("Passwords do not match");
						return false;
					}
				}
			</script> 
		<h1>New Account</h1>
		<div class='form'>
			<?php 
				echo form_open('account/createNew');
				echo form_label('Username'); 
				echo form_error('username');
				echo form_input('username',set_value('username'),"required");
				echo form_label('Password'); 
				echo form_error('password');
				echo form_password('password','',"id='pass1' required");
				echo form_label('Password Confirmation'); 
				echo form_error('passconf');
				echo form_password('passconf','',"id='pass2' required oninput='checkPassword();'");
				echo form_label('First');
				echo form_error('first');
				echo form_input('first',set_value('first'),"required");
				echo form_label('Last');
				echo form_error('last');
				echo form_input('last',set_value('last'),"required");
				echo form_label('Email');
				echo form_error('email');
				echo form_input('email',set_value('email'),"required");
				echo form_label('Please enter the image bellow');
				echo "<br>";
				echo '<img id="captcha" src="../../securimage/securimage_show.php" alt="CAPTCHA Image" />';
				echo '<input type="text" name="captcha_code" size="10" maxlength="6" />';
				echo '<a href="#" onclick="document.getElementById(\'captcha\').src = \'../../securimage/securimage_show.php?\' + Math.random(); return false">[ Different Image ]</a>';
				echo form_submit('submit', 'Register');
				echo form_close();
			?>	
		</div>
	</div>
<?php include('inc/footer.php'); ?>
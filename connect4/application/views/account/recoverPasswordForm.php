<?php include('inc/header.php'); ?> 
	<div class='content'>
		<h1>Recover Password</h1>
		<div class='form'>
			<?php 
				if (isset($errorMsg)) {
					echo "<p>" . $errorMsg . "</p>";
				}

				echo form_open('account/recoverPassword');
				echo form_label('Email'); 
				echo form_error('email');
				echo form_input('email',set_value('email'),"required");
				echo form_submit('submit', 'Recover Password');
				echo form_close();
			?>
		</div>
	</div>	
<?php include('inc/footer.php'); ?>
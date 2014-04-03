
<!DOCTYPE html>

<html>
	<head>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
	<script>

		var otherUser = "<?= $otherUser->login ?>";
		var user = "<?= $user->login ?>";
		var status = "<?= $status ?>";

		//Loading the images:
		var board_img = new Image();
		var ball_red = new Image();
		var ball_yellow = new Image();

		board_img.src = "<?= base_url()?>/images/board.png"
		ball_red.src = "<?= base_url()?>/images/red.png"
		ball_yellow.src = "<?= base_url()?>/images/yellow.png"
		
			
		$(function(){
		//Game code here
		
			var context = document.getElementById("game_canvas").getContext("2d");
			var context_width = context.canvas.width;
			var context_height = context.canvas.height;

			var board_initial_x = 128;
			var board_initial_y = 35;

			function draw() {
				//clear div:
				context.clearRect(0, 0, context_width, context_height);
				
				//1. draw balls
				
				
				//2. draw board
				context.drawImage(board_img, -100, 0, 800, 600);
			};


			
			//redraw calling:
			var animateInterval = setInterval(draw, 100);

			
				
		


			
			$('body').everyTime(2000,function(){
					if (status == 'waiting') {
						$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
								if (data && data.status=='rejected') {
									alert("Sorry, your invitation to play was declined!");
									window.location.href = '<?= base_url() ?>arcade/index';
								}
								if (data && data.status=='accepted') {
									status = 'playing';
									$('#status').html('Playing ' + otherUser);
								}
								
						});
					}
					var url = "<?= base_url() ?>board/getMsg";
					$.getJSON(url, function (data,text,jqXHR){
						if (data && data.status=='success') {
							var conversation = $('[name=conversation]').val();
							var msg = data.message;
							if (msg.length > 0)
								$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
						}
					});
			});

			$('form').submit(function(){
				var arguments = $(this).serialize();
				var url = "<?= base_url() ?>board/postMsg";
				$.post(url,arguments, function (data,textStatus,jqXHR){
						var conversation = $('[name=conversation]').val();
						var msg = $('[name=msg]').val();
						$('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
						});
				return false;
				});	
		});
	
	</script>
	</head> 
<body>  
	<h1>Game Area</h1>

	<div>
	Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  
	</div>
	
	<canvas id='game_canvas' width='800' height='600'></canvas>
	
	
	<div id='status'> 
	<?php 
		if ($status == "playing")
			echo "Playing " . $otherUser->login;
		else
			echo "Wating on " . $otherUser->login;
	?>
	</div>
	
<?php 
	
	echo form_textarea('conversation');
	
	echo form_open();
	echo form_input('msg');
	echo form_submit('Send','Send');
	echo form_close();
	
?>
	
	
	
	
</body>

</html>


<?php include('inc/header.php'); ?>
	<div class:'content'>
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
		<script>

			var otherUser = "<?= $otherUser->login ?>";
			var user = "<?= $user->login ?>";
			var status = "<?= $status ?>";

			var player = "<?= $player ?>";

			//Loading the images:
			var board_img = new Image();
			var ball_red = new Image();
			var ball_yellow = new Image();

			board_img.src = "<?= base_url()?>/images/board.png"
			ball_red.src = "<?= base_url()?>/images/red.png"
			ball_yellow.src = "<?= base_url()?>/images/yellow.png"
				
			$(function(){
			//Game code here
			
				var canvas = document.getElementById("game_canvas");
				var context = document.getElementById("game_canvas").getContext("2d");
				var context_width = context.canvas.width;
				var context_height = context.canvas.height;

				var rect = canvas.getBoundingClientRect();
			    
				var board_initial_x = 31;
				var board_initial_y = 101;

				//movement flags: if the user did a movement, did receives true and column is where the movement was done.
				var movement_user1 = {did: false, column: 0, height: 0};
				var movement_user2 = {did: false, column: 0, height: 0};

				var turn = 1; //user1 = 1; user2 = 2;	
						
				var next_move = 0;

				//creating the array of possible ball positions
				var ball = new Array();
				for(var i = 0; i<7; i++){ 
					ball[i] = new Array(); 
				}
				
				/*Obs: 
				About the vector ball:
				if the ball value is 0, it's empty;
				if the ball value is 1, it is red;
				if the ball value is 2, it is yellow;
				*/

				for(var i = 0; i<6; i++){
					for(var j = 0; j<7; j++){ 
						ball[i][j] = 0; //if it is 0, the position does not have a ball
					}
				}
				
				
				//function to draw and redraw the game.
				function draw() {
					//clear div:
					context.clearRect(0, 0, context_width, context_height);

					//1. draw balls
						for(var i = 0; i<7;i++){
							for(var j=0;j<7;j++){
								//if ball value is 0:
									//does nothing
								if(ball[i][j]==1){
									//it is red:
									context.drawImage(ball_red, board_initial_x+j*77-(j*2/(12-j)), board_initial_y+i*77-(i*2/(12-i)), 66,66);
								} else if(ball[i][j]==2){
									//it is yellow:
									context.drawImage(ball_yellow, board_initial_x+j*77-(j*2/(12-j)), board_initial_y+i*77-(i*2/(12-i)), 66,66);
								}
							}
						}
						//draw next move:
						if(turn == 1 && user==player)
							context.drawImage(ball_red, board_initial_x+next_move*77-(next_move*2/(12-next_move)), 10, 66,66);
						else if(turn == 2 && user!=player)
							context.drawImage(ball_yellow, board_initial_x+next_move*77-(next_move*2/(12-next_move)), 10, 66,66);
						
						//if there is a ball falling:
						//need to know where. -> object movement_userx.did; movement_userx.column.
						
						//red ball movement
						if(movement_user1.did){
							var k = movement_user1.column;
							context.drawImage(ball_red, board_initial_x+k*77-(k*2/(12-k)), movement_user1.height, 66,66);
							movement_user1.height += 30;
							for(var i = 0; i<5;i++){
								if(ball[i+1][k] != 0){
									if(movement_user1.height > board_initial_y+i*77-(k*2/(12-k))){
										movement_user1.did=false;
										movement_user1.height = 0;
										ball[i][k] = 1;
									}
								} 
							}
							if(movement_user1.height > 500){
								movement_user1.did = false;
								movement_user1.height = 0;
								ball[5][k] = 1;
							}
						}
						//yellow ball movement
						if(movement_user2.did){
							var k = movement_user2.column;
							context.drawImage(ball_yellow, board_initial_x+k*77-(k*2/(12-k)), movement_user2.height, 66,66);
							movement_user2.height += 30;
							for(var i = 0; i<5;i++){
								if(ball[i+1][k] != 0){
									if(movement_user2.height > board_initial_y+i*77-(k*2/(12-k))){
										movement_user2.did=false;
										movement_user2.height = 0;
										ball[i][k] = 2;
									}
								} 
							}
							if(movement_user2.height > 500){
								movement_user2.did = false;
								movement_user2.height = 0;
								ball[5][k] = 2;
							}
						}

					//2. draw board
					context.drawImage(board_img, -100, 66, 800, 600);

	// 				if the ball value is 0, it's empty;
	// 				if the ball value is 1, it is red;
	// 				if the ball value is 2, it is yellow;


				};


				
				



				
				//draw calling:
				var animateInterval = setInterval(draw, 100);
			    
			    
			    //update next move:
			    context.canvas.addEventListener('mousemove', function(evt) {
				var mousePos = {
			          x: evt.clientX - rect.left,
			          y: evt.clientY - rect.top};
			      //alert("Mouse Position: "+ mousePos.x + ', ' + mousePos.y);
			      if(mousePos.x<553)
			      	next_move = Math.floor(((mousePos.x*6)/480));    
			      }, false);

			    
				context.canvas.addEventListener("click", function(event){

					if(ball[0][next_move]==0){
						if (turn == 1){
							if(user==player){
								//turn = 2;
								movement_user1.did = true;
								movement_user1.column = next_move;
								//postMove -> similar to postMsg (see bellow in chat);
								didMove(next_move);							
							} else{
								alert("The other player needs to make a move, wait please.");
							}					
							
						} else if (turn == 2){
							if(user!=player){
								//turn = 1;
								movement_user2.did = true;
								movement_user2.column = next_move;
								//postMove;
								didMove(next_move);
							} else{
								alert("The other player needs to make a move, wait please.");
							}
						}					
					}
					
				}, false);
				/*
				//getMove here!!!
				function update() {
					var url = "<?= base_url() ?>board/getMove";
					$.getJSON(url, function (data,text,jqXHR){
						if (data && data.status=='success') {
							var tState = data.tState;
						}
						if(tState < 7){
							if(turn==1){
								movement_user1.did = true;
								movement_user1.column = tState;
								turn=2;
							} else if(turn == 2){
								movement_user2.did = true;
								movement_user2.column = tState;
								turn=1;
							}
						}	
					});
				}
*/
				function didMove(move_place){
					if(player == user){
						turn = 2;
						//debuging:
						player = otherUser;
						//post move into Json here
					}				
					else{
						turn = 1;
						//debuging:
						player = user;
						//post move into json here
						
					}
				}
						
					
				
				
		//		var updating = setInterval(100,update);
					
			


				
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
		<br>
		<div class='user_bar'>
			Hello, <?= $user->fullName() ?>!  
			<div class="buttons">
				<span id='button'><?= anchor('account/logout','Logout') ?></span>
			</div>
		</div>
		
		<div class='gameboard'>
			<div id='game'> 
				<p><?php 
					if ($status == "playing")
						echo "Playing with " . $otherUser->login;
					else
						echo "Waiting for " . $otherUser->login;
				?></p>
				<canvas id='game_canvas' width='600' height='555'></canvas>
			</div>
			<div class='conversation'>
				<div class='chat'>	
					<?php 
					
					echo form_textarea('conversation');
					
					echo form_open();
					echo form_input('msg');
					echo form_submit('Send','Send');
					echo form_close();
					
					?>
				</div>
			</div>

		</div>
		

	</div>
<?php include('inc/footer.php'); ?>
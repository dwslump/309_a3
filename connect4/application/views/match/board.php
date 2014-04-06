<?php include('inc/header.php'); ?>
	<div class:'content'>
		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
		<script>

			var otherUser = "<?= $otherUser->login ?>";
			var user = "<?= $user->login ?>";
			var status = "<?= $status ?>";

			var player = "<?= $player ?>";

			var myColor;

			var localMove = false;
			
			if(player == user)
				myColor = "red";
			else
				myColor = "yellow";
			
			//Loading the images:
			var board_img = new Image();
			var ball_red = new Image();
			var ball_yellow = new Image();

			board_img.src = "<?= base_url()?>/images/board.png"
			ball_red.src = "<?= base_url()?>/images/red.png"
			ball_yellow.src = "<?= base_url()?>/images/yellow.png"

			var firstLoad = true;
			var fix = false;
			var returned_board = new Array();	
			for(var i = 0; i<7; i++){ 
				returned_board[i] = new Array(); 
			}
			
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
				update();

// 				didMove(ball,true);
// 				turn=1;
				//function to draw and redraw the game.
				function draw() {
					//clear div:					
					update();
					context.clearRect(0, 0, context_width, context_height);
					var board_changed = false;

					for(var i = 0; i<6; i++){
						for(var j = 0; j<7; j++){
							if(returned_board[i][j] !=ball[i][j]){
// 								if(!firstLoad)
// 									console.log("Differs on ["+i+"]["+j+"]");
								board_changed = true;
							}
						}
					}
					
// 					console.log("Player " + player);
// 					console.log("User " + user);					
// 					console.log("Other user " + otherUser);
					
					if(board_changed){		
						
// 						console.log("LOCAL BOARD");
// 						console.log(ball);
// 						console.log("REMOTE BOARD");
// 						console.log(returned_board);
						
						for(var i = 0; i<6; i++){
							for(var j = 0; j<7; j++){
								ball[i][j] = returned_board[i][j];
							}
						}
// 						if(!localMove && !firstLoad){ // The other player made the movement
						if(!localMove && !firstLoad){
							console.log("BOARD CHANGED!");
							
// 							console.log("Your oponent made a move!!!");
							if(player == user){
								player = otherUser;
							}else{
								player = user;
							}
// 						}else if(!firstLoad){
// 							localMove = false;
						}
						
						firstLoad = false;
					}

						

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
						if(myColor=="red")
							context.drawImage(ball_red, board_initial_x+next_move*77-(next_move*2/(12-next_move)), 10, 66,66);
						else if(myColor=="yellow")
							context.drawImage(ball_yellow, board_initial_x+next_move*77-(next_move*2/(12-next_move)), 10, 66,66);
						
						//if there is a ball falling:
						//need to know where. -> object movement_userx.did; movement_userx.column.
						
						//red ball movement
						if(localMove){
							var k = movement_user1.column;
							if(myColor=="red")
								context.drawImage(ball_red, board_initial_x+k*77-(k*2/(12-k)), movement_user1.height, 66,66);
							else
								context.drawImage(ball_yellow, board_initial_x+k*77-(k*2/(12-k)), movement_user1.height, 66,66);
							movement_user1.height += 30;
							for(var i = 0; i<5;i++){
								if(ball[i+1][k] != 0){
									if(movement_user1.height > board_initial_y+i*77-(k*2/(12-k))){
										localMove=false;
										movement_user1.height = 0;
										if(myColor=="red")
											ball[i][k] = 1;
										else
											ball[i][k] = 2;
									}
								} 
							}
							if(movement_user1.height > 500){
								localMove = false;
								movement_user1.height = 0;
								if(myColor=="red")
									ball[i][k] = 1;
								else
									ball[i][k] = 2;
							}
							didMove(ball);
							if(player == user){
								player = otherUser;
							}else{
								player = user;
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
							verifyWinner();	
							if(player==user){
									localMove = true;
									movement_user1.did = true;
									movement_user1.column = next_move;	
				
							}else{
								alert("The other player needs to make a move, wait please.");
							}																				
					}
					
				}, false);

				function verifyWinner(){
					//line
					for(var i=0;i<6;i++){
						var win_red = 0;
						var win_yellow = 0;
						for(var j=0;j<7;j++){
							if(ball[i][j] == 1){
								win_red++;
							} else win_red=0;
							if(ball[i][j] == 2){
								win_yellow++;
							} else win_yellow=0;
							
							if (win_red == 4){
								alert(user + " won!");
							}
							if (win_yellow == 4){
								alert(otherUser + " won!");
							}
						}
					}
					//column
					for(var j=0;j<7;j++){
						var win_red = 0;
						var win_yellow = 0;
						for(var i=0;i<7;i++){
							if(ball[i][j] == 1){
								win_red++;
							} else win_red=0;
							if(ball[i][j] == 2){
								win_yellow++;
							} else win_yellow=0;
							
							if (win_red == 4){
								alert(user + " won!");
							}
							if (win_yellow == 4){
								alert(otherUser + " won!");
							}
						}
					}
					//diagonal /
					for(var k=0;k<4;k++){
						for(var i=0;i<3;i++){
							for(j=0;j<4;j++){
								if(ball[j+i][j+k] == 1){
									win_red++;
								} else win_red=0;
								if(ball[j+i][j+k] == 2){
									win_yellow++;
								} else win_yellow=0;
								
								if (win_red == 4){
									alert(user + " won!");
								}
								if (win_yellow == 4){
									alert(otherUser + " won!");
								}
							}
						}
					}
					//diagonal \
					for(var k=0;k<4;k++){
						for(var i=0;i<3;i++){
							for(j=3;j>=0;j--){
								if(ball[j+i][3-j+k] == 1){
									win_red++;
								} else win_red=0;
								if(ball[j+i][3-j+k] == 2){
									win_yellow++;
								} else win_yellow=0;
								
								if (win_red == 4){
									alert(user + " won!");
								}
								if (win_yellow == 4){
									alert(otherUser + " won!");
								}
							}
						}
					}
				}
					
				function update(){
					var action = new Object();
					action.user = user;
					action.otherUser = otherUser;
					$.getJSON('<?= base_url() ?>board/GetGame', action,function( data ) {							
						if(data.ball!= "none"){							
// 							for(var i = 0; i<6; i++){
// 								for(var j = 0; j<7; j++){
// 									returned_board[i][j] = data.ball[i][j]; //if it is 0, the position does not have a ball
// 								}
// 							}
							returned_board = $.extend(true, {},data.ball);
						}else{
							for(var i = 0; i<6; i++){
								for(var j = 0; j<7; j++){
									returned_board[i][j] = 0;
								}
							}
						}
						
						});
					
				}
				
				function didMove(move_place){					
					var action = new Object();
					action.user = user;
					action.otherUser = otherUser;						
					action.move_place = move_place;					
					  $.ajax({
						    type: 'POST',
						    url: '<?= base_url() ?>board/SendGame',
						    data: action				  
						  });			
				}
				
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
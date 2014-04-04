
<table>
<?php 
	if ($users) {
		foreach ($users as $user) {
			if ($user->id != $currentUser->id) {
?>		
			<tr>
			<td> 
			<span id="users"><?= anchor("arcade/invite?login=" . $user->login,$user->fullName()) ?></span>
			</td>
			</tr>

<?php 	
			}
		}
	}
?>

</table>
<?php

	
	//värden för pdo
	$host="localhost";
	$dbname="guestbook";
	$username="guestbook";
	$password="123456";
	//göra pdo
	$dsn="mysql:host=$host;dbname=$dbname";
	$attr=array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
	$pdo = new PDO($dsn, $username, $password, $attr);
	if($pdo){
		//har något postats? skriv till databas
		if(!empty($_POST))
		{
			$_POST = null; 
			$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT); 
			$post = filter_input(INPUT_POST, 'post', FILTER_SANITIZE_SPECIAL_CHARS); 
			// echo $user_id.",".$post;
			$statement = $pdo->prepare("INSERT INTO posts (date, user_id, post) VALUES (NOW(), :user_id, :post)");
			$statement->bindParam(":user_id", $user_id);
			$statement->bindParam(":post",$post);
			$statement->execute();	
		}
		
		//visa postforumlär för att skriva inlägg
		?>
		
		<form action="index.php" method="post">
		<p>
			<label for="user_id">User: </label>
			<select nam "user_id">
				<?php
				foreach ($pdo->query("SELECT * FROM users ORDER BY name") as $row){
				echo "<option value={$row['id']}>{$row['name']}</option>";
			}
				
				?>
			<select>
		</p>
		<p>
			<label for="post">Post: </label>
			<input type="text" name="Post"/>
			</select>
		</p>
		<input type="submit" value="Post"/>
		</form>
		<hr />
		
		<?php
		
		//visa alla användare (ul)
		echo "<ul>";
		echo "<li><a href=\"index.php\"> All users</a> </li>";
			foreach ($pdo->query("SELECT * FROM users ORDER BY name") as $row){
				echo "<li><a href=\"?user_id={$row['id']}\">{$row['name']} </a></li>";
			}
		echo "</ul>";
		echo "<hr />";
		//om user klickat på ett namn, visa dess inlägg
		//annars visa alla inlägg
		if(!empty($_GET)){
		
			$_GET = null; 
			$user_id = filter_input(INPUT_GET, 'user_id');
			$statement = $pdo ->prepare("SELECT posts,*,user.name FROM posts 
										JOIN users on USERS-id=posts.user_id WHERE user_id=:user_id ODER BY date");
			$statement ->bindParam (":user_id", $user_id);
			if ($statement ->execute())
			{
				while ($row = $statement ->fetch())
				{
					echo "<p> {$row['date']} by {$row['user_name']} <br> />
					{$row['post']}</p>";
				}
			}
			
		}else{
			foreach ($pdo->query("SELECT posts.*,users.name AS user_name FROM posts JOIN users ON users.id=posts.user_id ORDER BY date") as $row){
				echo "<p> {$row['date']} by {$row['user_name']} <br> />
					{$row['post']}</p>";
			}
		}
	}else{
		echo "Not connected";
	}
	
?>
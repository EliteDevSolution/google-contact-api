<?php
//including the database connection file
include_once("config.php");
include_once("contact.php");
//fetching data in descending order (lastest entry first)
//$result = mysql_query("SELECT * FROM users ORDER BY id DESC"); // mysql_query is deprecated
//$result = mysqli_query($mysqli, "SELECT * FROM users ORDER BY id DESC"); // using mysqli_query instead
?>

<html>
<head>	
	<title>Homepage</title>
	<?php
		include_once("style.php");
	?>
</head>
<body>
	<div class="text-center">
		<h1>Google Contact API</h1>
	</div>
	<br>
	<div class="container text-right">
	<?php
	if (isset($auth)) {
    	print "<a class='btn btn-primary' href='$auth'>Connect Me!</a>";
     } else
     {
        print "<a class='btn btn-danger' href='?logout'>Logout</a>";
	 }?>
	</div>

	<div class="container">	
		<table width='100%' class ="table table-striped table-borderd table-hover" style="cursor: pointer;">
			<thead>
				<tr >
					<th>#</th>
					<th>Name</th>
					<th width="40%">Content</th>
					<th>PhotoUrl</th>
					<th>selfUrl</th>
					<th>EditUrl</th>
					<th>Email</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$index = 0;
			 if(isset($contactsArray))
			 {
			 	 mysqli_query($mysqli, "TRUNCATE contacts");
			 	foreach ($contactsArray as $value) { $index++ ?>
					<tr>
						<td title="<?=$value['id']?>"><?=$index?></td>
						<td title="<?=$value['name']?>"><?=mb_substr($value['name'], 0,20).'...'?></td>
						<td title="<?=$value['content']?>"><?=mb_substr($value['content'],0,20).'...'?></td>
						<td title="<?=$value['photoURL']?>"><?=mb_substr($value['photoURL'],0,20).'...'?></td>
						<td title="<?=$value['selfURL']?>"><?=mb_substr($value['selfURL'],0,20).'...'?></td>
						<td title="<?=$value['editURL']?>"><?=mb_substr($value['editURL'],0,20).'...'?></td>	
						<td title="<?=$value['realemail']?>"><?=mb_substr($value['realemail'],0,20).'...'?></td>	
						<?php 
							extract($value);
						    $insertQuery = "Insert contacts value(NULL, '$id','$name','$content','$photoURL','$selfURL','$editURL','$realemail')";
							mysqli_query($mysqli, $insertQuery);
						?>
					</tr>
			<?php }
			 }
			?>
			</tbody>
		</table>
  </div>
</body>
</html>

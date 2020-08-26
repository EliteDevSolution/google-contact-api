<?php
// including the database connection file
include_once("config.php");
include_once("style.php");

if(isset($_POST['update']))
{	

	$id = mysqli_real_escape_string($mysqli, $_POST['id']);
	
	$name = mysqli_real_escape_string($mysqli, $_POST['name']);
	$age = mysqli_real_escape_string($mysqli, $_POST['age']);
	$email = mysqli_real_escape_string($mysqli, $_POST['email']);	
	$address = mysqli_real_escape_string($mysqli, $_POST['address']);

	// checking empty fields
	if(empty($name) || empty($age) || empty($email) || empty($address)) {	
			
		if(empty($name)) {
			echo "<font color='red'>Name field is empty.</font><br/>";
		}
		
		if(empty($age)) {
			echo "<font color='red'>Age field is empty.</font><br/>";
		}
		
		if(empty($email)) {
			echo "<font color='red'>Email field is empty.</font><br/>";
		}	
		if (empty($address)) {
				echo "<font color = 'red'>Address field is empty.</font><br/>";
		}	
	} else {	
		//updating the table
		$result = mysqli_query($mysqli, "UPDATE users SET name='$name',age='$age',email='$email' WHERE id=$id");
		
		//redirectig to the display page. In our case, it is index.php
		header("Location: index.php");
	}
}
?>
<?php
//getting id from url
$id = $_GET['id'];

//selecting data associated with this particular id
$result = mysqli_query($mysqli, "SELECT * FROM users WHERE id=$id");

while($res = mysqli_fetch_array($result))
{
	$name = $res['name'];
	$age = $res['age'];
	$email = $res['email'];
	$address = $res['address'];
}
?>
<html>
<head>	
	<title>Edit Data</title>
</head>

<body>
	<a href="index.php">Home</a>
	<br/><br/>

	<form action = "edit.php" method="post" name = "form1">
 
		<div class="form-group row">
			<label for="inputName" class="col-sm-2 col-form-label">Name</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
			</div>
		</div>
		<div class="form-group row">
			<label for="inputAge" class="col-sm-2 col-form-label">Age</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="age" value="<?php echo $age ?>">
			</div>
		</div>
		<div class="form-group row">
			<label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="email" value="<?php echo $email ?>">
			</div>
		</div>

		<div class = "form-group row">
			<label for = "inputAddress" class = "col-sm-2 col-form-label">Address</label>
			<div class = "col-sm-10">
				<input type="text" class = "form-control" name="address" value = "<?php echo $address ?>">
			</div>
		</div>

		<div class="form-group row">
			<div class="offset-sm-2 col-sm-10">
				<input type="submit" value="Update" name="update" class="btn btn-primary"/>
			</div>
		</div>
	</form>

</body>
</html>

<?php
session_start();
include('includes/config.php');

if (isset($_POST['login'])) {
	$email = $_POST['username'];
	$password = $_POST['password'];

	$sql = "SELECT username, password FROM user WHERE username = :email";
	$query = $dbh->prepare($sql);
	$query->bindParam(':email', $email, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);

	if ($result) {
		if (password_verify($password, $result->password)) {
			$_SESSION['alogin'] = $email;
			echo "<script type='text/javascript'>document.location = 'admin/index.php';</script>";
		} else {
			$err = "Incorrect Password";
		}
	} else {
		$err = "Invalid Username";
	}
}

$title = "Admin Login";
?>

<!doctype html>
<html lang="en" class="no-js">
<?php include('includes/_head.php'); ?>

<body style="background: linear-gradient(to right, #134e4a, #166534); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
	<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
		<div class="card shadow-lg p-5" style="max-width: 500px; width: 100%; border-radius: 15px; background-color: #ffffffdd;">
			<h2 class="text-center mb-4 text-dark fw-bold">Admin Login</h2>

			<?php if (!empty($err)) : ?>
				<div class="alert alert-danger text-center"><?php echo htmlentities($err); ?></div>
			<?php endif; ?>

			<form method="post">
				<div class="mb-3">
					<label for="username" class="form-label">Username</label>
					<input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
				</div>

				<div class="mb-4">
					<label for="password" class="form-label">Password</label>
					<input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
				</div>

				<button type="submit" name="login" class="btn btn-success w-100">Login</button>
			</form>

			<footer class="text-center mt-4">
				<small class="text-muted">&copy; 2025-<?php echo date("Y"); ?> PAVIMS. All rights reserved.</small>
			</footer>
		</div>
	</div>

	<?php include('includes/_scripts.php'); ?>
</body>
</html>

<?php
session_start();
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
} else {
	// Code for change password	
	if (isset($_POST['submit'])) {
		$password = $_POST['password'];
		$newpassword = $_POST['newpassword'];
		$username = $_SESSION['alogin'];

		// Fetch the current password hash from the database
		$sql = "SELECT Password FROM user WHERE username=:username";
		$query = $dbh->prepare($sql);
		$query->bindParam(':username', $username, PDO::PARAM_STR);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);

		// Check if the entered current password matches the stored hashed password
		if ($result && password_verify($password, $result->Password)) {
			// Hash the new password
			$newpassword_hashed = password_hash($newpassword, PASSWORD_DEFAULT);

			// Update the password in the database
			$con = "UPDATE user SET password = :newpassword, last_updated = NOW() WHERE username = :username";
			$chngpwd1 = $dbh->prepare($con);
			$chngpwd1->bindParam(':username', $username, PDO::PARAM_STR);
			$chngpwd1->bindParam(':newpassword', $newpassword_hashed, PDO::PARAM_STR);

			if ($chngpwd1->execute()) {
				$success = "Password updated successfully!";
				$redirect = "index.php";
			} else {
				$err = "Failed to update password!";
				$redirect = "change-password.php";
			}
		} else {
			$err = "Your current password is incorrect!";
			$redirect = "change-password.php";
		}
	}
	$title = "Admin |  Change Password";
?>


	<!doctype html>
	<html lang="en" class="no-js">
	<?php include('includes/_head.php'); ?>

	<body>
		<?php include('includes/header.php'); ?>
		<div class="ts-main-content">
			<?php include('includes/leftbar.php'); ?>
			<div class="content-wrapper">
				<div class="container-fluid">

					<div class="row">
						<div class="col-md-12">

							<h2 class="page-title">Change Password</h2>

							<div class="row">
								<div class="col-md-10">
									<div class="panel panel-default">
										<div class="panel-heading">Form fields</div>
										<div class="panel-body">
											<form method="post" name="chngpwd" class="form-horizontal" onSubmit="return valid();">
												<div class="form-group">
													<label class="col-sm-4 control-label">Current Password</label>
													<div class="col-sm-8">
														<input type="password" class="form-control" name="password" id="password" required>
													</div>
												</div>
												<div class="hr-dashed"></div>

												<div class="form-group">
													<label class="col-sm-4 control-label">New Password</label>
													<div class="col-sm-8">
														<input type="password" class="form-control" name="newpassword" id="newpassword" required>
													</div>
												</div>
												<div class="hr-dashed"></div>

												<div class="form-group">
													<label class="col-sm-4 control-label">Confirm Password</label>
													<div class="col-sm-8">
														<input type="password" class="form-control" name="confirmpassword" id="confirmpassword" required>
													</div>
												</div>
												<div class="hr-dashed"></div>

												<div class="form-group">
													<div class="col-sm-8 col-sm-offset-4">

														<button class="btn" style="background-color: #337518; color: white;" name="submit" type="submit">Save changes</button>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Loading Scripts -->
		<?php include('includes/_scripts.php'); ?>
	</body>

	</html>
<?php } ?>
<?php include_once('../common/header.php'); ?>
<?php include_once('style_sheet.php'); ?>
<!-- pagina originale pages/examples/forgot-password-v2.html -->

<body class="hold-transition login-page">
	<div class="login-box">
		<div class="card card-outline card-primary">
			<div class="card-header text-center">
				<h1 class="h1">Sell Masters</h1>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Hai dimenticato la password? Qui puoi recuperare facilmente una nuova password.</p>
				<form action="#" id='forgot-password' name='forgot-password' method="post">
					<div class="input-group mb-3">
						<input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
					</div>
					<div class="feedback text-red mb-3">
						<span id="feedback"></span>
					</div>
					<div class="row">
						<div class="col-12">
							<button type="submit" class="btn btn-primary btn-block">Recupera password</button>
						</div>
						<!-- /.col -->
					</div>
				</form>
				<p class="mt-3 mb-1">
					<a href="index.php">Accedi</a>
				</p>
			</div>
			<!-- /.login-card-body -->
		</div>
	</div>
	<!-- /.login-box -->

	<?php include_once('../common/required_script.php'); ?>
	<?php include_once('required_script.php'); ?>

</body>

</html>
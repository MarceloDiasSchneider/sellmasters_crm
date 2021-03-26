<?php include_once('../common/header.php'); ?>
<!-- pagina originale pages/examples/login-v2.html -->

<body class="hold-transition login-page">
	<div class="login-box">
		<!-- /.login-logo -->
		<div class="card card-outline card-primary">
			<div class="card-header text-center">
				<h1 class="h1">Sell Masters</h1>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Accedi per iniziare la tua sessione</p>

				<form id="autenticazione" name="autenticazione" action="#" method="post">
					<div class="input-group mb-3">
						<input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" id="password" name="password" class="form-control" placeholder="Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno un numero e una lettera maiuscola e minuscola e almeno 8 o più caratteri">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="feedback text-red mb-3">
						<span id="feedback"></span>
					</div>
					<div class="row">
						<div class="col-8">
							<div class="icheck-primary">
								<input type="checkbox" id="remember">
								<label for="remember">
									Remember Me
								</label>
							</div>
						</div>
						<!-- /.col -->
						<div class="col-4">
							<button type="submit" id="bottone_autenticazione" class="btn btn-primary btn-block">Accedi</button>
						</div>
						<!-- /.col -->
					</div>
				</form>

				<p class="mb-1">
					<a href="#">Ho dimenticato la mia password</a>
				</p>
			</div>
			<!-- /.card-body -->
		</div>
		<!-- /.card -->
	</div>
	<!-- /.login-box -->

	<?php include_once('../common/required_script.php'); ?>
	<?php include_once('required_script.php'); ?>

	<!-- AdminLTE App -->
	<script src="../../dist/js/adminlte.min.js"></script>
</body>

</html>
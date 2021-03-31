<?php include_once('../common/header.php'); ?>
<?php include_once('style_sheet.php'); ?>
<!-- pagina originale pages/examples/recover-password-v2.html -->

<body class="hold-transition login-page">
	<div class="login-box">
		<div class="card card-outline card-primary">
			<div class="card-header text-center">
				<h1>Sell Masters</h1>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Sei solo a un passo dalla tua nuova password, recuperala ora.</p>
				<form action="#" id="recover-password" name="recover-password" method="post">
					<div class="input-group mb-3">
						<input type="password" id="password" name="password" class="form-control" placeholder="Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno un numero e una lettera maiuscola e minuscola e almeno 8 o più caratteri">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" id="confirm-password" name="confirm-password" class="form-control" placeholder="Conferma Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno un numero e una lettera maiuscola e minuscola e almeno 8 o più caratteri">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<button type="submit" class="btn btn-primary btn-block">Cambiare la password</button>
						</div>
						<!-- /.col -->
					</div>
					<input type="hidden" id="email" name="email" value="<?php echo $_REQUEST['email'] ?>">
					<input type="hidden" id="code" name="code" value="<?php echo $_REQUEST['code'] ?>">
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
<?php include_once('../common/header.php'); ?>
<?php include_once('../common/sessione.php'); ?>
<?php include_once('style_sheet.php'); ?>
<!-- pagina originale pages/from/general.html | General form -->
<!-- pagina originale pages/tables/data.html | Datatables -->
<!-- pagina originale pages/UI/modals.html | Toastr -->

<body class="hold-transition sidebar-mini">
	<div class="wrapper">
		<?php
		include_once('../common/navbar.php');
		include_once('../common/sidebar.php');
		?>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1>Gestione utenti</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active">Utenti</li>
							</ol>
						</div>
					</div>
				</div><!-- /.container-fluid -->
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<!-- form registra utente -->
							<div class="card card-primary">
								<div class="card-header">
									<h3 class="card-title">Nuovo utente</h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse">
											<i class="fas fa-minus"></i>
										</button>
									</div>
								</div>
								<!-- /.card-header -->
								<div class="card-body">
									<!-- form start -->
									<form action='#' id='nuovo_utente' name='nuovo_utente' method='post'>
										<div class="card-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="Nome">Nome</label>
														<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" maxlength="30" required>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="Cognome">Cognome</label>
														<input type="text" class="form-control" id="cognome" name="cognome" placeholder="Cognome" maxlength="30">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="data-nascita">Data di nascita</label>
														<input type="date" class="form-control" id="data_nascita" name="data_nascita" placeholder="Data di nascita">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="codiceFiscale">Codice Fiscale</label>
														<input type="text" class="form-control" id="codice_fiscale" name="codice_fiscale" placeholder="Codice Fiscale" pattern="^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$" title="Codice fiscale errato." maxlength="16">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="telefono">Telefono</label>
														<input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono" maxlength="16">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Livello</label>
														<select id="livello" name="livello" class="form-control" required>
															<!-- <option disabled selected></option> -->
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="email">Email</label>
														<input type="email" class="form-control" id="email" name="email" placeholder="Email" maxlength="50" required>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="password">Password</label>
														<input type="password" class="form-control" id="password" name="password" placeholder="Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno un numero, una lettera maiuscola, minuscola e almeno 8 caratteri." required>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="verificaPassword">Verifica password</label>
														<input type="password" class="form-control" id="verificaPassword" name="verificaPassword" placeholder="Verifica password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno un numero, una lettera maiuscola, minuscola e almeno 8 caratteri." required>
													</div>
												</div>
											</div>
										</div>
										<input type="hidden" id="attivo" name="attivo" value="1">
										<input type="hidden" id="codiceSessione" name="codiceSessione" value="<?php echo $_SESSION['codiceSessione'] ?>">
										<!-- /.card-body -->
										<div class="card-footer">
											<button type="submit" id="bottone_registra_utente" class="btn btn-primary">Registra</button>
											<button type="submit" id="bottone_nuovo_utente" class="btn btn-primary d-none">Indietro a nuovo utente</button>
										</div>
									</form>
								</div>
							</div>
							<!-- /.card -->
						</div>
						<!--/.form registra utente -->
					</div>
					<!-- /.row -->
					<div class="row">
						<div class="col-md-12">
							<!-- tabella con tutti gli utente registrate -->
							<div class="card card-primary">
								<div class="card-header">
									<h3 class="card-title">Tutti gli utenti</h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse">
											<i class="fas fa-minus"></i>
										</button>
									</div>
								</div>
								<!-- /.card-header -->
								<div class="card-body">
									<table id="utenti" class="table table-bordered table-striped">
									</table>
								</div>
								<!-- /.card-body -->
							</div>
							<!-- /.tabella con tutti gli utente registrate -->
						</div>
					</div>
				</div><!-- /.container-fluid -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
			<!-- Control sidebar content goes here -->
		</aside>
		<!-- /.control-sidebar -->

		<?php include_once('../common/footer.php'); ?>

	</div>
	<!-- ./wrapper -->

	<?php include_once('../common/required_script.php'); ?>
	<?php include_once('required_script.php'); ?>

</body>

</html>
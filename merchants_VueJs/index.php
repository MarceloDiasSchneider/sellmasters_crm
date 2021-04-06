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
							<h1>Merchants</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active">Merchants</li>
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
									<h3 class="card-title">Nuovo commerciante</h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse">
											<i class="fas fa-minus"></i>
										</button>
									</div>
								</div>
								<!-- /.card-header -->
								<div class="card-body">
									<!-- form start -->
									<form action='#' id='merchant' name='merchant' method='post'>
										<div class="card-body">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="nome">Nome</label>
														<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" maxlength="45" required>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="nome_sociale">Nome sociale</label>
														<input type="text" class="form-control" id="nome_sociale" name="nome_sociale" placeholder="Nome sociale" maxlength="100">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="merchant_id">ID Commerciante</label>
														<input type="text" class="form-control" id="merchant_id" name="merchant_id" placeholder="ID Commerciante" maxlength="45" required>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="mws">MWS</label>
														<input type="text" class="form-control" id="mws" name="mws" placeholder="MWS" maxlength="450">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="interval_between_check">Intervallo tra i controlli</label>
														<input type="number" class="form-control" id="interval_between_check" name="interval_between_check" placeholder="24 ore">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="nome_contatto">Nome contatto</label>
														<input type="text" class="form-control" id="nome_contatto" name="nome_contatto" placeholder="Nome contatto" maxlength="100">
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
														<label for="email">Email</label>
														<input type="email" class="form-control" id="email" name="email" placeholder="Email" maxlength="100">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="indirizzo">Indirizzo</label>
														<input type="text" class="form-control" id="indirizzo" name="indirizzo" placeholder="Indirizzo" maxlength="100">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="numero_civico">Numero civico</label>
														<input type="text" class="form-control" id="numero_civico" name="numero_civico" placeholder="Numero civico" maxlength="45">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="citta">Citta</label>
														<input type="text" class="form-control" id="citta" name="citta" placeholder="Citta" maxlength="45">
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label for="cap">Cap</label>
														<input type="text" class="form-control" id="cap" name="cap" placeholder="Cap" maxlength="45">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="stato">Stato</label>
														<input type="text" class="form-control" id="stato" name="stato" placeholder="Stato" maxlength="45">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label for="provincia">Provincia</label>
														<input type="text" class="form-control" id="provincia" name="provincia" placeholder="Provincia" maxlength="45">
													</div>
												</div>
											</div>
										</div>
										<input type="hidden" id="attivo" name="attivo" value="1">
										<input type="hidden" id="codiceSessione" name="codiceSessione" value="<?php echo $_SESSION['codiceSessione'] ?>">
										<!-- /.card-body -->
										<div class="card-footer">
											<button type="submit" id="register" class="btn btn-primary">Registra</button>
											<button type="submit" id="back_register" class="btn btn-primary d-none">Indietro a nuovo commerciante</button>
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
							<!-- table with all merchants -->
							<div class="card card-primary">
								<div class="card-header">
									<h3 class="card-title">Tutti gli commercianti</h3>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-card-widget="collapse">
											<i class="fas fa-minus"></i>
										</button>
									</div>
								</div>
								<!-- /.card-header -->
								<div class="card-body">
									<table id="merchants" class="table table-bordered table-striped">
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
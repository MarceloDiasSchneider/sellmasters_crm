<?php include_once('../common/header.php'); ?>
<?php include_once('../common/sessione.php'); ?>
<?php include_once('style_sheet.php'); ?>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php
		include_once('../common/navbar.php');
		include_once('../common/sidebar.php');
		?>
        <input type="hidden" id="codiceSessione" name="codiceSessione" value="<?php echo $_SESSION['codiceSessione']; ?>">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Registri di Accessi</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Registri di Accessi</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Registri di Accessi</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="registri_accessi" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Utente</th>
                                                <th>Data</th>
                                                <th>Ip</th>
                                                <th>Remote Port</th>
                                                <th>User Agent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            </tfoot>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
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
<?php include_once('../common/header.php');
include_once('stylesheet.php'); ?>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once('../common/navbar.php');
        include_once('../common/sidebar.php');

        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?php $_SESSION['app_name']; ?></h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                                <li class="breadcrumb-item active"><?php $pagename; ?></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Cerca Ordine</h3>
                                </div>
                                <div class="card-body">
                                    <form action='#' id='form_cerca_ordine' name='form_cerca_ordine' method='post'>
                                        <div class="row">
                                            <div class="col-3">
                                                <input type="data" name='data_ordine' class="form-control" placeholder="data">
                                            </div>
                                            <div class="col-3">
                                                <input type="text" name='amazon_id' class="form-control" placeholder="amazon id">
                                            </div>
                                            <div class="col-3">
                                                <input type="text" name='merchant' class="form-control" placeholder="merchant">
                                            </div>
                                            <div class="col-3">
                                                <button type="submit" class="btn btn-primary" id='bottone_cerca_ordine'>Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card-body -->
                            </div>

                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <table id='griglia' class='table table-stripped'></table>
                        </div>
                    </div>



                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">sellmasters </a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b>1.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->
  <?php   include_once('required_script.php');  ?>
    
</body>

</html>
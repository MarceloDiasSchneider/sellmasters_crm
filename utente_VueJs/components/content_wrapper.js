app.component('content_wrapper', {
	props: {
		codice_sessione: {
			type: String,
			// requided: true
		}
	},
    template:
        /*html*/
        `<!-- Content Wrapper. Contains page content -->
        <div class="wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-wrapper">
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
                        <register_user @refresh_datatables="refresh_datatables" :codice_sessione="codice_sessione"></register_user>
                        <all_users @test="get_user_data" ref="refresh_datatables"></all_users>
                    </div><!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->`,
    data(){
        return {

        }
    },
    methods: {
        get_user_data(id){
            console.log('estou funcionanado ' + id );
        },
        refresh_datatables(){
            console.log(app);
            // console.log(this.$refs);
        }
    }
})
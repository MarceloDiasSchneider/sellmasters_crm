app.component('content_wrapper', {
	props: {
		codice_sessione: {
			type: String
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
                                <h1>Ordini</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Ordini</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <orders></orders>
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
        // send to the main js which page must appear as active on sidebar
        send_page(){
            this.$emit('page', 'orders')
        }
    },
    beforeMount() {
        this.send_page()
    }
})
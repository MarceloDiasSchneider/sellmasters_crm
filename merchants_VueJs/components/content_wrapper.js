app.component('content_wrapper', {
    props: {
		codice_sessione: {
			type: String
		}
	},
    template:
        /*html*/
        `<div class="wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Commerciante</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Commerciante</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <register_merchant @refresh_datatables="refresh_datatables" ref="register" :codice_sessione="codice_sessione"></register_merchant>
                        <all_merchants @merchant_data="get_merchant_data" ref="all_merchants"></all_merchants>
                    </div><!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content -->
        </div>`,
    methods: {
        // call a register merchant method get_merchant_data()
        get_merchant_data(id){
            this.$refs.register.get_merchant_data(id)
        },
        // call a method on the all merchantes component 
        refresh_datatables(){
            this.$refs.all_merchants.refresh_datatables()
        },
        // send to the main js which page must appear as active on sidebar
        send_page(){
            this.$emit('page', 'merchants_VueJs')
        }
    },
    beforeMount() {
        this.send_page()
    }
})
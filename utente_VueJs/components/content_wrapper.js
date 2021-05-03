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
                        <register_user @refresh_datatables="refresh_datatables" ref="register" :codice_sessione="codice_sessione"></register_user>
                        <all_users @user_data="get_user_data" ref="all_users"></all_users>
                    </div><!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content -->
        </div>`,
    methods: {
        // call a register user method get_user_data()
        get_user_data(id){
            this.$refs.register.get_user_data(id)
        },
        // call a method on the all users component 
        refresh_datatables(){
            this.$refs.all_users.refresh_datatables()
        },
        // send to the main js which page must appear as active on sidebar
        send_page(){
            this.$emit('page', 'Gestire Utenti', 'utente_VueJs')
        }
    },
    beforeMount() {
        this.send_page()
    }
})
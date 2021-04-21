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
                                <h1>Profilo di accesso</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active">Profilo di accesso</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <profile ref="register" :codice_sessione="codice_sessione" @refresh_datatables="refresh_datatables"></profile>
                        <all_profiles ref="all_profiles" @profile_data="profile_data"></all_profiles>
                    </div><!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->`,
    methods: {
        // call a register profile method
        profile_data(id){
            this.$refs.register.get_profile_data(id)
        },
        // call a method on the all lelves component 
        refresh_datatables(){
            this.$refs.all_profiles.refresh_datatables()
        },
        // send to the main js which page must appear as active on sidebar
        send_page(){
            this.$emit('page', 'profiles')
        },
    },
    beforeMount() {
        this.send_page()
    }
})
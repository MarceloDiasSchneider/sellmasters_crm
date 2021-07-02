app.component('content_wrapper', {
    props: {
        codice_sessione: {
            type: String
        },
    },
    template:
    /*html*/
        `<div class="wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-app" @click="reset_form" data-toggle="modal" data-target="#modal-xl">
                                    <i class="fas fa-plus-circle text-success"></i>Nuovo
                                </button>
                                <button type="button" class="btn btn-app" @click="edit" data-toggle="modal" data-target="#modal-xl" :class="[ selected_row ? '' : 'disabled' ]" :disabled=" !selected_row ">
                                    <i class="fas fa-edit text-warning"></i>Modificare
                                </button>
                                <button type="button" class="btn btn-app" @click="block" :class="[ selected_row ? '' : 'disabled' ]" :disabled=" !selected_row ">
                                    <i class="fas fa-ban text-danger"></i>Blocca
                                </button>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <register_user 
                            ref="register" 
                            @refresh_datatables="refresh_datatables" 
                            @set_selected_row="set_selected_row" 
                            :codice_sessione="codice_sessione" 
                            :selected_row="selected_row">
                        </register_user>
                        <all_users 
                            ref="all_users" 
                            @set_selected_row="set_selected_row" 
                            :selected_row="selected_row">
                        </all_users>
                    </div><!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content -->
        </div>`,
    data() {
        return {
            selected_row: null
        }
    },
    methods: {
        // call a method on the all users component 
        refresh_datatables() {
            this.$refs.all_users.refresh_datatables()
        },
        // send to the main js which page must appear as active on sidebar
        send_page() {
            this.$emit('page', 'Gestire Utenti', 'utente_VueJs')
        },
        set_selected_row(data) {
            this.selected_row = data
        },
        reset_form() {
            this.$refs.register.reset_form()
        },
        edit() {
            this.$refs.register.get_user_data()
        },
        block() {
            this.$refs.all_users.toggle_user_active()
        },
    },
    beforeMount() {
        this.send_page()
    }
})
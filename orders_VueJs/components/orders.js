app.component('orders', {
    template:
    /*html*/
        `<div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Gestione di Ordini</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action='#' id='user_form' name='user_form' method='post' @submit.prevent="get_data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="start_date">Data inizio</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" v-model="startDate">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_date">Data fine</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" v-model="endDate">
                                    </div>
                                </div>
                            </div>                        
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-sm-right ml-1 mt-1" v-show="!datatables">
                                        <button type="submit" class="btn btn-primary">Ricerca</button>
                                    </div>
                                    <div class="float-sm-right ml-1 mt-1" v-show="datatables">
                                        <button type="submit" class="btn btn-primary" @click.prevent="refresh_datatables">Ricaricare</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <!-- table -->
                        <table id="orders" class="table table-bordered table-striped">
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="overlay dark" v-show="loading">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <!-- /.loading -->
                </div>
            </div>
            <!-- /.tabella con tutti gli utente registrate -->
        </div>
        <!-- /.row -->`,
    data() {
        return {
            // variables to bind the form
            startDate: '2021-04-20',
            endDate: '2021-04-21',
            // variable to control between load datatables or reload
            datatables: false,
            // variable to control the loading card
            loading: false,
        }
    },
    methods: {
        get_data() {
            this.datatables = true
            let self = this
            $("#orders").DataTable({
                'ajax': {
                    type: "POST",
                    url: "http://51.91.97.200/sellmaster/api_sellmasters/ordini_mondotop.php",
                    data: function(){ 
                        return {'data_inizio': self.startDate, 'data_fine': self.endDate} 
                    },
                    dataType: "json",
                    async: false,
                    dataSrc: ""
                },
                columns: [
                    { title: "ID ordine", data: "order_id" },
                    { title: "Carrier", data: "carrier" },
                    { title: "Market Status", data: "market_status" },
                    { title: "Data di acquisto", data: "purchase_date" },
                ],
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#orders_wrapper .col-md-6:eq(0)');
        },
        // refresh the datatables
        refresh_datatables() {
            this.loading = true
            let self = this
            $('#orders').DataTable().ajax.reload(
                function(){self.loading = false;}, 
                false
            );
        },
    },
    mounted() {

    }
})
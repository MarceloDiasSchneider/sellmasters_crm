app.component('orders', {
    template:
    /*html*/
        `<div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title" @click="log">Gestione di Ordini</h3>
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
                                        <input type="date" class="form-control" id="start_date" name="start_date" v-model="startDate" :max="endDate">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_date">Data fine</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" v-model="endDate" :min="startDate">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Commerciante</label>
                                        <select id="merchants_id" name="merchants_id" class="form-control" v-model="merchants_id">
                                            <option disabled selected value="0">seleziona un commerciante</option>
                                            <option v-for="option in select_options" :value="option.id">{{ option.merchants_name }}</option>
                                        </select>
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
            merchants_id: '',
            // array to hold the select options
            select_options: [],
            // variable to control between load datatables or reload
            datatables: false,
            // variable to control the loading card
            loading: false,
        }
    },
    methods: {
        // get all select options
        get_select_options() {
            this.loading = true
                // set options to send with the post request
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({ 'action': 'get_merchants' })
            }
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json()
                    switch (data.code) {
                        case '500':
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message)
                            break;
                        case '200':
                            // format the data to show the selct options
                            data.merchatsData.forEach(data => {
                                this.select_options.push({ id: data.merchant_id, merchants_name: data.nome })
                            });
                            break;
                        default:
                            break;
                    }
                    this.loading = false
                })
                // report an error if there is
                .catch(error => {
                    this.errorMessage = error;
                    console.error('There was an error!', error);
                });
        },
        // initiate the dataTables
        get_data() {
            this.datatables = true
            let self = this
            $("#orders").DataTable({
                'ajax': {
                    type: "POST",
                    url: "http://51.91.97.200/sellmaster/api_sellmasters/ordini_mondotop.php",
                    data: function() {
                        return { 'data_inizio': self.startDate, 'data_fine': self.endDate }
                    },
                    dataType: "json",
                    async: false,
                    dataSrc: ""
                },
                "dom": 'BlrtipRS',
                // R = colReorder: true,
                // B = button: true,
                "scrollX": true,
                "responsive": false,
                "scrollCollapse": false,
                "lengthChange": true,
                "pageLength": 20,
                columns: [
                    { title: "ID ordine", data: "order_id" },
                    { title: "Carrier", data: "carrier" },
                    { title: "Market Status", data: "market_status" },
                    { title: "Data di acquisto", data: "purchase_date" },
                    { title: "Prezzo articolo", data: "item_price" },

                    { title: "ID ordine", data: "order_id" },
                    { title: "Carrier", data: "carrier" },
                    { title: "Market Status", data: "market_status" },
                    { title: "Data di acquisto", data: "purchase_date" },
                    { title: "Prezzo articolo", data: "item_price" },

                    { title: "ID ordine", data: "order_id" },
                    { title: "Carrier", data: "carrier" },
                    { title: "Market Status", data: "market_status" },
                    { title: "Data di acquisto", data: "purchase_date" },
                    { title: "Prezzo articolo", data: "item_price" },

                    { title: "ID ordine", data: "order_id" },
                    { title: "Carrier", data: "carrier" },
                    { title: "Market Status", data: "market_status" },
                    { title: "Data di acquisto", data: "purchase_date" },
                    { title: "Prezzo articolo", data: "item_price" },
                ],
                "lengthChange": false,
                "autoWidth": true,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#orders_wrapper .col-md-6:eq(0)');
        },
        // refresh the datatables
        refresh_datatables() {
            this.loading = true
            let self = this
            $('#orders').DataTable().ajax.reload(
                function() { self.loading = false; },
                false
            );
        },
        log() {
            console.log(this.startDate);
            console.log(this.endDate);
        }
    },
    mounted() {
        this.get_select_options()
    }
})
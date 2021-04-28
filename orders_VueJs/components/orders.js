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
                        <div class="row">
                        <div class="col-lg-10 col-xl-8">
                        <form action='#' id='user_form' name='user_form' method='post' @submit.prevent="get_orders">
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
                        </div>
                        </div>
                        <hr>
                        <!-- table -->
                        <table id="orders" class="table table-bordered table-striped nowrap">
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
            startDate: null,
            endDate: null,
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
        // set a default date to the input 
        set_today() {
            // today
            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            let yyyy = today.getFullYear();

            today = `${yyyy}-${mm}-${dd}`;

            this.endDate = today
            // X days ago
            let x = 2
            let sevenDaysAgo = new Date(Date.now() - x * 24 * 60 * 60 * 1000)
            dd = String(sevenDaysAgo.getDate()).padStart(2, '0');
            mm = String(sevenDaysAgo.getMonth() + 1).padStart(2, '0'); //January is 0!
            yyyy = sevenDaysAgo.getFullYear();

            sevenDaysAgo = `${yyyy}-${mm}-${dd}`;

            this.startDate = sevenDaysAgo
        },
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
        get_orders() {
            this.datatables = true
            let self = this
            // formatting function for row child details 
            function format ( data ) {
                dati_finanziari = null
                if (data.dati_finanziari){
                    dati_finanziari = '<table cellpadding="3" cellspacing="0" border="0" style="padding-left:50px;">'+
                        '<tr>'+
                            '<td>Dati finanziari:</td>'+
                            '<td>'+data.dati_finanziari+'</td>'+
                        '</tr>'+
                    '</table>';
                } else {
                    dati_finanziari = '<table cellpadding="3" cellspacing="0" border="0" style="padding-left:50px;">'+
                    '<tr>'+
                        '<td>Nessun dati finanziari</td>'+
                    '</tr>'+
                '</table>';
                }
                return dati_finanziari
            }
            // set the datatables
            $("#orders").DataTable({
                // P search panes Q custom search
                "dom": 'l<"row mb-2"<"col-sm-12 col-md-8"B><"col-sm-12 col-md-4"f>><"row mb-2"<"col-sm-12"rt>><"row mb-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>RQ', 
                'ajax': {
                    type: "GET",
                    url: "orders_manipulator_class.php",
                    data: function() {
                        return {
                            'startDate': self.startDate,
                            'endDate': self.endDate,
                            'merchant_id': self.merchants_id
                        }
                    },
                    dataType: "json",
                    async: false,
                    dataSrc: ""
                },
                searchPanes: {
                    cascadePanes: true
                },
                "pageLength": 10,
                select: true,
                // rowGroup: {
                //     dataSrc: 'purchase_date',
                // },
                columns: [
                    { 
                        "title": "",
                        "className": 'dtr-control',
                        "orderable": false,
                        "selected": false,
                        "data": null,
                        "defaultContent": "<i class='fas fa-plus-square'></i>"
                    },
                    { "title": "Order id", data: "order_id"},
                    { "title": "Merchant id", data: "merchant_id" },
                    { "title": "Purchase date", data: "purchase_date" },
                    { "title": "Account id", data: "account_id" },
                    { "title": "Market status", data: "market_status" },
                    { "title": "Paese", data: "paese" },
                    { "title": "Recipient name", data: "recipient_name" },
                    { "title": "Currency", data: "currency" },
                    { "title": "Item price", data: "item_price" },
                    { "title": "Shipping price", data: "shipping_price" },
                    { "title": "Item promotion discount", data: "item_promotion_discount" },
                    { "title": "Total order", data: "total_order" },
                    { "title": "Quantity purchased", data: "quantity_purchased" },
                    { "title": "Sku", data: "sku" },
                    { "title": "Manufacturer", data: "manufacturer" },
                    { "title": "Category", data: "category" },
                    { "title": "Marketplace", data: "marketplace" },
                    { "title": "Weight", data: "weight" },
                    { "title": "Fee people amazon it", data: "fee_people_amazon_it" },
                    { "title": "Is business order", data: "is_business_order" },
                    { "title": "Title", data: "title" },
                    { "title": "Is prime", data: "isprime" },
                    { "title": "Fulfillment channel", data: "fulfillment_channel" },
                    { "title": "Group price", data: "group_price" },
                    { "title": "Numberofitems shipped", data: "numberofitems_shipped" },
                    { "title": "Numberofitems unshipped", data: "numberofitems_unshipped" },
                    { "title": "Fee people amazon de", data: "fee_people_amazon_de" },
                    { "title": "Fee people amazon es", data: "fee_people_amazon_es" },
                    { "title": "Fee people amazon fr", data: "fee_people_amazon_fr" },
                    { "title": "Fee people amazon uk", data: "fee_people_amazon_uk" },
                    { "title": "Shipping tax", data: "shipping_tax" },
                    { "title": "Commission by lengow", data: "commission_by_lengow" },
                    { "title": "Tracking number", data: "tracking_number" },
                    { "title": "Carrier", data: "carrier" },
                    { "title": "Price", data: "price" },
                    // { "title": "Dati finanziari", data: "dati_finanziari" },
                ],
                "order": [[ 3, "desc" ]],
                "responsive": false,
                "scrollX": true,
                "lengthChange": true,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "print", "colvis", "searchPanes"] // "PDF" 
            }).buttons().container().appendTo('#orders_wrapper .col-md-8:eq(0)');

            // Add event listener for opening and closing details
            $('#orders tbody').on('click', 'td.dtr-control', function () {
                let tr = $(this).closest('tr');
                let row = $('#orders').DataTable().row( tr );
         
                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                }
                else {
                    // Open this row
                    row.child( format(row.data()) ).show();
                }
            } );
        },
        // refresh the datatables
        refresh_datatables() {
            $('#orders').DataTable().ajax.reload(null, false);
        },
    },
    mounted() {
        this.get_select_options()
        this.set_today()
    }
})
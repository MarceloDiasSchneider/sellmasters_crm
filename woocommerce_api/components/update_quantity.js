app.component('update_quantity', {
    props: {
        codice_sessione: {
            type: String
        },
        api_url: {
            type: String,
            required: true
        },
        consumer_key: {
            type: String,
            required: true
        },
        consumer_secret: {
            type: String,
            required: true
        },
        api_version: {
            type: String,
            required: true
        }
    },
    template:
    /*html*/
        `<div class="modal fade" id="modal-update-quantity">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h3 class="card-title">Update stock</h3>
                    <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-update-quantity" @click="reset">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST" id="update_quantity" name="update_quantity" @submit.prevent="split_stock_before_update">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="exampleInputFile">Choose a file</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" accept=".csv" ref="file" class="custom-file-input" id="file" @change="get_file" required>
                                            <label class="custom-file-label" for="file">Choose a file</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" v-show="stock.length">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">Preview</h3>
                
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                            <i class="fas fa-expand"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body table-responsive p-0">
                                                <table class="table table-hover text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>SKU</th>
                                                            <th>Stock quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="stock in stock">
                                                            <td>{{ stock.sku }}</td>
                                                            <td>{{ stock.stock_quantity }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="float-right ml-1 mt-1">
                                    {{ report_error }}
                                    <button type="submit" id="button" class="btn btn-primary">Update stock</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5" v-show="updating">
                            <div class="col-12">
                                <p>Updating {{ update_completed + ' / ' + stockToUpdate }}</p>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" :aria-valuenow="update_completed" aria-valuemin="0" :aria-valuemax="stockToUpdate" :style="'width: ' + completed + '%'">
                                    <span class="sr-only">40% Complete (success)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Success {{ stock_success_updated.length ? stock_success_updated.length : '' }}</h3>
                        
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p v-for="stock in stock_success_updated">
                                            {{ 'sku: '+ stock.sku }}<br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Error {{ stock_error_updated.length ? stock_error_updated.length : '' }}</h3>
                    
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p v-for="stock in stock_error_updated">
                                            {{ 'sku: '+ stock.sku }}<br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- /.modal-body -->
                <div class="overlay" v-show="false">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>
            </div><!-- /.modal-content -->
        </div><!--/.modal-dialog -->
    </div>`,
    data() {
        return {
            loading: false,
            updating: false,
            file: null,
            splice: 100,
            report_error: null,
            update_completed: 0,
            stockToUpdate: 0,
            stock: [],
            stock_success_updated: [],
            stock_error_updated: [],
        }
    },
    methods: {
        update_quantity(stock) {
            this.loading = true
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'update_stock_quantity',
                    'codice_sessione': this.codice_sessione,
                    'api_url': this.api_url,
                    'consumer_key': this.consumer_key,
                    'consumer_secret': this.consumer_secret,
                    'api_version': this.api_version,
                    'stock': stock
                })
            }
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json()
                    switch (data.code) {
                        case 500:
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message)
                            break;
                        case 406:
                            // reporting a forbidden request. ex: session code doesn't match
                            alert(data.message)
                            document.location.href = '../autenticazione_VueJs';
                            break;
                        case 400:
                            // repoting a bad request
                            toastr.error(data.message)
                            this.update_completed += stock.length
                            break;
                        case 200:
                            // reporting a success message
                            toastr.success(data.message)
                            data.data.stock_success.forEach(stock => {
                                this.stock_success_updated.push(stock)
                            });
                            data.data.stock_error.forEach(stock => {
                                this.stock_error_updated.push(stock)
                            });
                            this.update_completed += stock.length
                            if (this.update_completed == this.stockToUpdate) {
                                this.loading = false
                            }
                            break;
                        default:
                            break;
                    }
                })
                // report an error if there is
                .catch(error => {
                    console.error('There was an error!', error);
                });
        },
        get_file() {
            let self = this
            this.file = this.$refs.file.files[0]
            const reader = new FileReader();
            reader.onload = function(e) {
                this.update_completed = 0
                const text = e.target.result
                const data = self.csvToArray(text)
                self.stock = []
                self.stock = data
                if (!self.stock.length) {
                    self.report_error = '1 - There is no quantities to update' 
                } else {
                    self.report_error = null
                }
            };
            if (this.file != null || this.file != undefined) {
                reader.readAsText(this.file);
            }
        },
        csvToArray(str, delimiter = ";") {
            // slice from start of text to the first \n index
            // use split to create an array from string by delimiter
            const headers = str.slice(0, str.indexOf("\n")).split(delimiter);
            
            // slice from \n index + 1 to the end of the text
            // use split to create an array of each csv value row
            const rows = str.slice(str.indexOf("\n") + 1).split("\n");

            // Map the rows
            // split values from each row into an array
            // use headers.reduce to create an object
            // object properties derived from headers:values
            // the object passed as an element of the array
            const arr = rows.map(function(row) {
                const values = row.split(delimiter);
                const el = headers.reduce(function(object, header, index) {
                    object[header] = values[index];
                    return object;
                }, {});
                return el;
            });

            // return the array
            return arr;
        },
        split_stock_before_update(){
            this.updating = true
            if (!this.stock.length) {
                this.report_error = '2 - There is no quantities to update'
            } else {
                this.stockToUpdate = this.stock.length
                this.update_completed = 0
                this.stock_success_updated = []
                this.stock_error_updated = []
                let stock = this.stock;
                while(stock.length) {
                    this.update_quantity( stock.splice(0,this.splice) )
                }
            }
        },
        reset() {
            this.loading = false
            this.updating = false
            this.file = null
            this.report_error = null
            this.update_completed = 0
            this.stockToUpdate = 0
            this.stock = []
            this.stock_success_updated = []
            this.stock_error_updated = []
            this.$refs.file.value = null;
        },
        show_modal() {
            $('#modal-update-quantity').modal('show')
        },
        hide_modal() {
            $('#modal-update-quantity').modal('hide')
        },
    },
    computed: {
        completed() {
            return this.update_completed * 100 / this.stockToUpdate
        },
    }
})
app.component('import_products', {
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
        `<div class="modal fade" id="modal-import-products">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="overlay" v-show="loading">
                    <i class="fas fa-2x fa-sync fa-spin"></i>
                </div>
                <div class="modal-header bg-primary">
                    <h3 class="card-title">Import products</h3>
                    <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-import-products" @click="reset">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST" id="import_products" name="import_products" @submit.prevent="split_products_before_inport">
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
                        <div class="col-md-12" v-show="products.length">
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
                                                            <th>Url image</th>
                                                            <th>Name</th>
                                                            <th>Category</th>
                                                            <th>Regular price</th>
                                                            <th>Stock quantity</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="product in products">
                                                            <td>{{ product.sku }}</td>
                                                            <td>{{ product.url_image }}</td>
                                                            <td>{{ product.name }}</td>
                                                            <td>{{ product.category }}</td>
                                                            <td>{{ product.regular_price }}</td>
                                                            <td>{{ product.stock_quantity }}</td>
                                                            <td>{{ product.status }}</td>
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
                                    <button type="submit" id="button" class="btn btn-primary">Import products</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5" v-show="importing">
                            <div class="col-12">
                                <p>Importing {{ import_completed + ' / ' + productToImport }}</p>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" :aria-valuenow="import_completed" aria-valuemin="0" :aria-valuemax="productToImport" :style="'width: ' + completed + '%'">
                                    <span class="sr-only">40% Complete (success)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Success {{ products_success_imported.length ? products_success_imported.length : '' }}</h3>
                        
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p v-for="product in products_success_imported">
                                            {{ 'sku: '+ product.sku }}<br>
                                            {{ 'name: ' + product.name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Error {{ products_error_imported.length ? products_error_imported.length : '' }}</h3>
                    
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p v-for="product in products_error_imported">
                                            {{ 'sku: '+ product.sku }}<br>
                                            {{ 'name: ' + product.name }}<br>   
                                            {{ 'error: ' + product.error }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- /.modal-body -->
            </div><!-- /.modal-content -->
        </div><!--/.modal-dialog -->
    </div>`,
    data() {
        return {
            loading: false,
            importing: false,
            file: null,
            report_error: null,
            import_completed: 0,
            productToImport: 0,
            products: [],
            products_success_imported: [],
            products_error_imported: [],
        }
    },
    methods: {
        import_products(product) {
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'create_product',
                    'codice_sessione': this.codice_sessione,
                    'api_url': this.api_url,
                    'consumer_key': this.consumer_key,
                    'consumer_secret': this.consumer_secret,
                    'api_version': this.api_version,
                    'product': product[0]
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
                            product[0]['error'] = data.message
                            this.products_error_imported.push(product[0])
                            this.import_completed += 1
                            break;
                        case 201:
                            // reporting a success message
                            toastr.success(data.message)
                            this.products_success_imported.push(product[0])
                            this.import_completed += 1
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
                this.import_completed = 0
                const text = e.target.result
                const data = self.csvToArray(text)
                self.products = []
                self.products = data
                if (!self.products.length) {
                    self.report_error = '1 - There is no products to import' 
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
        split_products_before_inport(){
            this.importing = true
            if (!this.products.length) {
                this.report_error = '2 - There is no products to import'
            } else {
                this.productToImport = this.products.length
                this.import_completed = 0
                this.products_success_imported = []
                this.products_error_imported = []
                let products = this.products;
                let splice = 1
                while(products.length) {
                    this.import_products( products.splice(0,splice) )
                }
            }
        },
        reset() {
            this.loading = false
            this.importing = false
            this.file = null
            this.report_error = null
            this.import_completed = 0
            this.productToImport = 0
            this.products = []
            this.products_success_imported = []
            this.products_error_imported = []
            this.$refs.file.value = null;
        },
        show_modal() {
            $('#modal-import-products').modal('show')
        },
        hide_modal() {
            $('#modal-import-products').modal('hide')
        },
    },
    computed: {
        completed() {
            return this.import_completed * 100 / this.productToImport
        },
    }
})
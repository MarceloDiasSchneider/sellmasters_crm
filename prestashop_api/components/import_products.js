app.component('import_products', {
    props: {
        codice_sessione: {
            type: String
        },
        api_url: {
            type: String,
            required: true
        },
        api_key: {
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
                    <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-import-products">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST" id="import_products" name="import_products" @submit.prevent="import_products">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="exampleInputFile">Choose a file</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" accept=".csv" ref="file" class="custom-file-input" id="file" @change="get_file" required>
                                            <label class="custom-file-label" for="file">Choose a file</label>
                                        </div>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Upload</span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Shop</label>
                                    <select id="shop_id" name="shop_id" class="form-control" v-model.number="shop_id" required @change="set_virtual_uri_create($event.target.selectedIndex)">
                                        <option v-for="(shop, index) in shops" :key="index" :value="shop.id">{{ shop.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="float-right ml-1 mt-1">
                            <button type="submit" id="button" class="btn btn-primary" >Import products</button>
                        </div>
                    </form>
                </div><!-- /.modal-body -->
            </div><!-- /.modal-content -->
        </div><!--/.modal-dialog -->
    </div>`,
    data() {
        return {
            loading: false,
            virtual_uri: 'group-1/store-1/api/',
            virtual_uri_create: null,
            file: null,
            products: [],
            shop_id: null,
            shops: []
        }
    },
    methods: {
        import_products() {
            this.loading = true
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'import_products',
                    'codice_sessione': this.codice_sessione,
                    'url': this.api_url,
                    'virtual_uri': this.virtual_uri_create,
                    'key': this.api_key,
                    'shop_id': this.shop_id,
                    'products': this.products
                })
            }
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json()
                    switch (data.code) {
                        case 501:
                            // reporting a not implemented request. ex: action does not match
                            alert(data.state)
                            console.log(data.message)
                            break;
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
                        case 401:
                            // reporting an unauthorized error. ex: password dasen't match 
                            toastr.warning(data.message)
                            break;
                        case 400:
                            // repoting a bad request
                            toastr.error(data.message)
                            break;
                        case 201:
                            // reporting a success message
                            toastr.success(data.message)
                            this.reset_form()
                            this.hide_modal()
                            break;
                        case 200:
                            // reporting a success message
                            toastr.success(data.message)
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
        get_file() {
            let self = this
            this.file = this.$refs.file.files[0]
            const reader = new FileReader();
            reader.onload = function(e) {
                const text = e.target.result
                const data = self.csvToArray(text)
                self.products = []
                self.products = data
                console.log(data);
            };
            if (this.file != null || this.file != undefined) {
                reader.readAsText(this.file);
            }
        },
        csvToArray(str, delimiter = ";") {
            // slice from start of text to the first \r index
            // use split to create an array from string by delimiter
            const headers = str.slice(0, str.indexOf("\r")).split(delimiter);
            
            // slice from \r index + 1 to the end of the text
            // use split to create an array of each csv value row
            const rows = str.slice(str.indexOf("\r") + 1).split("\r");

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
        set_virtual_uri_create(index) {
            this.virtual_uri_create = this.shops[index].virtual_uri + 'api/'
        },
        load_form_options() {
            this.loading = true
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'get_shops_data',
                    'codice_sessione': this.codice_sessione,
                    'url': this.api_url,
                    'virtual_uri': this.virtual_uri,
                    'key': this.api_key
                })
            }
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json()
                    switch (data.code) {
                        case 501:
                            // reporting a not implemented request. ex: action does not match
                            alert(data.state)
                            console.log(data.message)
                            break;
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
                        case 401:
                            // reporting an unauthorized error. ex: password dasen't match 
                            toastr.warning(data.message)
                            break;
                        case 400:
                            // repoting a bad request
                            toastr.error(data.message)
                            break;
                        case 201:
                            // reporting a success message
                            toastr.success(data.message)
                            break;
                        case 200:
                            // reporting a success message
                            toastr.success(data.message)
                            this.shops = []
                            this.shops = data.shops_data
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
        reset_form() {
            
        },
        show_modal() {
            $('#modal-import-products').modal('show')
        },
        hide_modal() {
            $('#modal-import-products').modal('hide')
        },

    },
})
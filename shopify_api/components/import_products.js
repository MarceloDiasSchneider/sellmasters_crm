app.component('import_products', {
    props: {
        codice_sessione: {
            type: String
        },
        api: {
            type: Object,
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
                                        <div class="input-group-append">
                                            <span class="input-group-text">Preview</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-show="importing">
                            <div class="col-12">
                                <p>Importing {{ import_completed + ' / ' + productLength }}</p>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" :aria-valuenow="import_completed" aria-valuemin="0" :aria-valuemax="productLength" :style="'width: ' + completed + '%'">
                                    <span class="sr-only">40% Complete (success)</span>
                                    </div>
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
            importing: false,
            import_completed: 0,
            file: null,
            products: [],
            productLength: 0,
            splice: 1
        }
    },
    methods: {
        import_products(products) {
            this.importing = true
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'import_products',
                    'codice_sessione': this.codice_sessione,
                    'api': this.api,
                    'products': products
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
                            // this.reset_form()
                            // this.hide_modal()
                            this.import_completed += this.splice
                            break;
                        case 200:
                            // reporting a success message
                            toastr.success(data.message)
                            break;
                        default:
                            break;
                    }
                    if ( this.productLength <= this.import_completed) {
                        this.importing = false
                        this.import_completed = 0
                        toastr.success('Import completed')
                    }
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
                this.import_completed = 0
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
            this.productLength = this.products.length
            let products = this.products;
            let splice = this.splice
            while(products.length) {
                this.import_products( products.splice(0,splice) )
            }
        },
        reset_form() {

        },
        show_modal() {
            $('#modal-import-products').modal('show')
        },
        hide_modal() {
            $('#modal-import-products').modal('hide')
        },
        addComplited() {
            this.import_completed += 1
        }
    },
    computed: {
        completed() {
            return this.import_completed * 100 / this.productLength
        },
    }
})
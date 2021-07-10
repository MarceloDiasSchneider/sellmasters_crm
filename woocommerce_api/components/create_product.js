app.component('create_product', {
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
        `<div class="modal fade" id="modal-create-product">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="overlay" v-show="loading">
                        <i class="fas fa-2x fa-sync fa-spin"></i>
                    </div>
                    <div class="modal-header bg-primary">
                        <h3 class="card-title">New product</h3>
                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-create-product">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="#" method="POST" id="new_product" name="new_product" @submit.prevent="create_product">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">SKU</label>
                                        <input class="form-control" type="text" id="sku" name="sku" placeholder="SKY" v-model="sku" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product name</label>
                                        <input class="form-control" type="text" id="name" name="name" placeholder="Product name" v-model="name" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product description</label>
                                        <input class="form-control" type="text" id="description" name="description" placeholder="Product description" v-model="description" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Category</label>
                                        <input class="form-control" type="text" id="category" name="category" placeholder="Category" v-model="category" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Regular price</label>
                                        <input class="form-control" type="number" step="0.01" id="regular_price" name="regular_price" placeholder="Price 10.00" v-model="regular_price" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Quantity</label>
                                        <input class="form-control" type="number" id="stock_quantity" name="stock_quantity" placeholder="Quantity" v-model="stock_quantity" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product url image</label>
                                        <input class="form-control" type="url" id="url_image" name="url_image" placeholder="Product url image" v-model="url_image" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select id="status" name="status" class="form-control" v-model="status" required>
                                            <optgroup label="Status">
                                                <option disabled selected value="0">seleziona un profile</option>
                                                <option v-for="status in status_options" :value="status">{{ status }}</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <!-- draft, pending, private and publish -->
                            </div>
                            <div class="float-right ml-1 mt-1">
                                <button type="submit" id="button-new-product" class="btn btn-primary">New product</button>
                            </div>
                        </form>
                    </div><!-- /.modal-body -->
                </div><!-- /.modal-content -->
            </div><!--/.modal-dialog -->
        </div>`,
    data() {
        return {
            loading: false,
            status_options: ['draft', 'pending', 'private', 'publish'],
            sku: null,
            name: null,
            description: null,
            category: null,
            regular_price: null,
            stock_quantity: null,
            url_image: null,
            status: null,
        }
    },
    methods: {
        create_product() {
            this.loading = true
            let product = {
                'sku': this.sku,
                'name': this.name,
                'description': this.description,
                'category': this.category,
                'regular_price': this.regular_price,
                'stock_quantity': this.stock_quantity,
                'url_image': this.url_image,
                'status': this.status,
            }
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
                    'product': product,
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
                        case 201:
                            // reporting a success message
                            toastr.success(data.message)
                            this.refresh_table()
                            this.hide_modal()
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
        refresh_table() {
            this.$emit('refresh_table')
        },
        reset_form() {
            this.sku =  null
            this.name =  null
            this.description =  null
            this.category =  null
            this.regular_price =  null
            this.stock_quantity =  null
            this.url_image =  null
            this.status = null
        },
        show_modal(){
            $('#modal-create-product').modal('show')
        },
        hide_modal(){
            $('#modal-create-product').modal('hide')
        },
    },
})
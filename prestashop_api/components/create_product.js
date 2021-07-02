app.component('create_product', {
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
                                        <label for="name">Price</label>
                                        <input class="form-control" type="number" step="0.01" id="price" name="price" placeholder="Price 10.00" v-model="price" required>
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product url image</label>
                                        <input class="form-control" type="url" id="url_image" name="url_image" placeholder="Product url image" v-model="url_image" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Product image format</label>
                                        <input class="form-control" type="text" id="image_mine" name="image_mine" placeholder="Image format" v-model="image_mine" required>
                                    </div>
                                </div>
                            </div>
                            <div class="float-right ml-1 mt-1">
                                <button type="submit" id="button" class="btn btn-primary" >New product</button>
                            </div>
                        </form>
                    </div><!-- /.modal-body -->
                </div><!-- /.modal-content -->
            </div><!--/.modal-dialog -->
        </div>`,
    data() {
        return {
            virtual_uri: 'group-1/store-1/api/',
            virtual_uri_create: null,
            loading: false,
            shops: [],
            shop_id: null,
            name: null,
            description: null,
            price: null,
            image_mine: 'image/jpeg',
            url_image:'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MXx8cHJvZHVjdHxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&w=1000&q=80'
        }
    },
    methods: {
        create_product() {
            this.loading = true
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'create_product',
                    'codice_sessione': this.codice_sessione,
                    'url': this.api_url,
                    'virtual_uri': this.virtual_uri_create,
                    'key': this.api_key,
                    'shop_id': this.shop_id,
                    'name': this.name,
                    'description': this.description,
                    'price': this.price,
                    'url_image': this.url_image,
                    'image_mine': this.image_mine 
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
        set_virtual_uri_create(index) {
            this.virtual_uri_create = this.shops[index].virtual_uri + 'api/'
        },
        reset_form() {
            this.name = null
            this.description = null
            this.price = null
            this.shop_id = null
        },
        show_modal(){
            $('#modal-create-product').modal('show')
        },
        hide_modal(){
            $('#modal-create-product').modal('hide')
        },
    },

})
app.component('show_products', {
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
    `<div class="row">
    <div class="col-md-12">
    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>    
            <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist" >
                <li class="nav-item" v-for="(shop, index) in shops_data">
                    <a class="nav-link" :id="'tab' + shop.id_shop_group + shop.id_shop" :key="index" data-toggle="pill" :href="'#shop' + shop.id_shop_group + shop.id_shop" role="tab" aria-controls="'shop' + shop.id_shop_group + shop.id_shop" aria-selected="false">{{ shop.name }}</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-five-tabContent">
                <div class="tab-pane fade" :id="'shop' + shop.id_shop_group + shop.id_shop" :key="shop.id" role="tabpanel" aria-labelledby="custom-tabs-five-overlay-tab" v-for="(shop, index) in shops_data">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                            <th>Id</th>
                            <th>Products</th>
                            <th>Price</th>
                            <th>Date created</th>
                            <th>Date update</th>
                            <th>active</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products_data[shop.id]">
                                <td>{{ product.id }}</td>
                                <td>{{ product.name[0].value }}</td>
                                <td>{{ product.price }}</td>
                                <td>{{ product.date_add }}</td>
                                <td>{{ product.date_upd }}</td>
                                <td>{{ product.active }}</td>
                            </tr> 
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right ml-1 mt-1">
                <button type="submit" id="button" class="btn btn-primary" @click="get_shops_and_products">Load products</button>
            </div>
        </div>
        <div class="overlay dark" v-show="loading">
            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>
    </div>
    </div>`,
    data() {
        return {
            loading: false,
            virtual_uri: 'group-1/store-1/api/',
            shops_data: [],
            products_data: []
        }
    },
    methods: {  
        get_shops_and_products() {
            this.loading = true
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'get_shops_and_products',
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
                            this.shops_data = []
                            this.shops_data = data.shops_data
                            this.products_data = []
                            this.products_data = data.products_data
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
    }
})
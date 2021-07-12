app.component('get_products', {
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
    `<div class="row">
    <div class="col-md-12">
    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>    
        </div>
        <div class="card-body">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Count</th>
                            <th>Id</th>
                            <th>SKU</th>
                            <th>Products</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Date created</th>
                            <th>Date update</th>
                            <th>active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product, key in products">
                            <td>{{ key + 1 }}</td>
                            <td>{{ product.id }}</td>
                            <td>{{ product.sku }}</td>
                            <td>{{ product.name }}</td>
                            <td>{{ product.price }}</td>
                            <td>{{ product.stock_quantity }}</td>
                            <td>{{ product.date_created }}</td>
                            <td>{{ product.date_modified }}</td>
                            <td>{{ product.status }}</td>
                        </tr> 
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right ml-1 mt-1">
                <button type="submit" id="button-get-products" class="btn btn-primary" @click="get_products">Load products</button>
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
            loading: null,
            products: null,
        }
    },
    methods: {  
        get_products() {
            this.loading = true
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'get_products',
                    'codice_sessione': this.codice_sessione,
                    'api_url': this.api_url,
                    'consumer_key': this.consumer_key,
                    'consumer_secret': this.consumer_secret,
                    'api_version': this.api_version,
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
                        case 200:
                            // reporting a success message
                            toastr.success(data.message)
                            this.products = data.data
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
        }
     },
})
app.component('content_wrapper', {
	props: {
		codice_sessione: {
			type: String
		}
	},
    template:
        /*html*/
        `<!-- Content Wrapper. Contains page content -->
        <div class="wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-wrapper">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-app" @click="new_product" data-toggle="modal" data-target="#modal-create-product">
                                    <i class="fas fa-plus-circle text-success"></i>Nuovo
                                </button>
                                <button type="button" class="btn btn-app" data-toggle="modal" data-target="#modal-import-products">
                                    <i class="fas fa-upload text-info"></i>Import
                                </button>
                                <button type="button" class="btn btn-app" data-toggle="modal" data-target="#modal-update-quantity">
                                    <i class="fas fa-boxes text-purple"></i>Update stock
                                </button>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <get_products
                            ref="get_products"
                            :codice_sessione="codice_sessione"
                            :api_url="api_url"
                            :consumer_key="consumer_key"
                            :consumer_secret="consumer_secret"
                            :api_version="api_version">
                        </get_products>
                        <create_product
                            ref="create_product"
                            :codice_sessione="codice_sessione"
                            :api_url="api_url"
                            :consumer_key="consumer_key"
                            :consumer_secret="consumer_secret"
                            :api_version="api_version">
                        </create_product>
                        <import_products
                            ref="import_products"
                            :codice_sessione="codice_sessione"
                            :api_url="api_url"
                            :consumer_key="consumer_key"
                            :consumer_secret="consumer_secret"
                            :api_version="api_version">
                        </import_products>
                        <update_quantity
                            ref="update_quantity"
                            :codice_sessione="codice_sessione"
                            :api_url="api_url"
                            :consumer_key="consumer_key"
                            :consumer_secret="consumer_secret"
                            :api_version="api_version">
                        </update_quantity>
                    </div><!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->`,
    data() {
        return {
            // api_url: 'http://localhost/wp-woo-commerce/wp-json/',
            // consumer_key: 'ck_2b58d19c44085a335db8e129c8a83fdf6f1dfbcc',
            // consumer_secret: 'cs_b537d3f8d0b45392c81beb5e5130880472c6f7e1',
            // api_version: 'wc/v3/',
            api_url: 'https://armarketvirtual.it/prova/wp-json/',
            consumer_key: 'ck_a7d325a0c7a877d9f71bbf0ad5d9dbbe2768287a',
            consumer_secret: 'cs_8f9ac7a92288eeeb49e5997d8b54754600b58c9f',
            api_version: 'wc/v3/',
        }
    },
    methods: {
        // send to the main js which page must appear as active on sidebar
        send_page(){
            this.$emit('page', 'mainpage', 'defaltpage')
        },
        new_product() {
            this.$refs.create_product.reset_form()
        },
        refresh_table() {
            this.$refs.get_products.get_products()
        },
    },
    created() {
        this.send_page()
    }
})
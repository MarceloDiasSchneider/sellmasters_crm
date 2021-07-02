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
                                <button type="button" class="btn btn-app" @click="reset_form_new_product" data-toggle="modal" data-target="#modal-create-product">
                                    <i class="fas fa-plus-circle text-success"></i>Nuovo
                                </button>
                                <button type="button" class="btn btn-app" @click="reset_form_import_product" data-toggle="modal" data-target="#modal-import-products">
                                    <i class="fas fa-upload text-info"></i>Import
                                </button>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <permissions 
                            :codice_sessione="codice_sessione" 
                            :api_key="api_key" 
                            :api_url="api_url">
                        </permissions>
                        <show_products 
                            :codice_sessione="codice_sessione"
                            :api_key="api_key"
                            :api_url="api_url">
                        </show_products>
                        <create_product
                            ref="create_product"
                            :codice_sessione="codice_sessione"
                            :api_key="api_key"
                            :api_url="api_url">
                        </create_product>
                        <import_products
                            ref="import_products"
                            :codice_sessione="codice_sessione"
                            :api_key="api_key"
                            :api_url="api_url">
                        </import_products>
                    </div><!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->`,
    data() {
        return {
            api_url: 'http://localhost/prestashop/',
            api_key: 'AGUEUXUW1HJ5Z7TXAUYLKLM757USW4SC',
        }
    },
    methods: {
        // send to the main js which page must appear as active on sidebar
        send_page(){
            this.$emit('page', 'mainpage', 'defaltpage')
        },
        reset_form_new_product() {
            this.$refs.create_product.reset_form()
            this.$refs.create_product.load_form_options()
        },
        reset_form_import_product() {
            this.$refs.import_products.reset_form()
            this.$refs.import_products.load_form_options()
        }
    },
    created() {
        this.send_page()
    }
})
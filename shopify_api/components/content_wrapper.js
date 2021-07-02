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
                        <button type="button" class="btn btn-app" data-toggle="modal" data-target="#modal-import-products">
                            <i class="fas fa-upload text-info"></i>Import
                        </button>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <show_products
                            :codice_sessione="codice_sessione"
                            :api="api">
                        </show_products>
                        <import_products
                            ref="import_products"
                            :codice_sessione="codice_sessione"
                            :api="api">
                        </import_products>
                    </div><!-- /.container-fluid -->
                </section>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->`,
    data(){
        return {
            api: {
                url: 'https://teste-api-sell-masters.myshopify.com/admin/api/2021-04/',
                key: '4414b59fe5ef18c792d646bc89c0ee4f',
                password: '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',
            }
        }
    },
    methods: {
        // send to the main js which page must appear as active on sidebar
        send_page(){
            this.$emit('page', 'mainpage', 'defaltpage')
        }
    },
    beforeMount() {
        this.send_page()
    }
})
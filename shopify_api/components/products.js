app.component('show_products', {
    props: {
        codice_sessione: {
            type: String
        },
        api: {
            type: Object,
            required: true
        },
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
            <div class="tab-content" id="custom-tabs-five-tabContent">
            </div>
        </div>
        <div class="card-footer">
            <div class="float-right ml-1 mt-1">
                <button type="submit" id="button" class="btn btn-primary">Load products</button>
            </div>
        </div>
        <div class="overlay dark" v-show="false">
            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>
    </div>
    </div>`,
})
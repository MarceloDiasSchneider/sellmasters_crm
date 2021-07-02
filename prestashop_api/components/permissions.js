app.component('permissions', {
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
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">API Permissions</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info bg-light float-left ml-1" v-for="permission in permissions">
                            <p>{{ permission }}</p>    
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="float-right ml-1 mt-1">
                            <button type="submit" id="button" class="btn btn-primary" @click="get_api_permissions">Show permission</button>
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
            permissions: [],
            virtual_uri: 'group-1/store-1/api/'
        }
    },
    methods: {
        get_api_permissions() {
            this.loading = true
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'get_api_permissions',
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
                            this.permissions = []
                            data.permissions.forEach(permission => {
                                this.permissions.push(permission)
                            });
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
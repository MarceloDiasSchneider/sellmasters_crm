app.component('profile', {
    props: {
        codice_sessione: {
            type: String
        }
    },
    template:
    /*html*/
        `<div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Registra un nuovo profile</h3>
                        <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    </div>
                    <div class="card-body">
                        <form action="#" id="description" @submit.prevent="insert_or_update_profile">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="description">Profile descrizione</label>
                                        <input type="text" class="form-control" id="description" placeholder="ex: Finanziario" maxlength="20" required v-model="description">
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <!-- checkbox parent -->
                                    <label for="description">Autorizzazioni della pagina</label>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox mb-3" v-for="main in pages">
                                            <input type="checkbox" :id="main[0].main" class="custom-control-input" :checked="!mainPages.find(pages => pages.main == main[0].main && !pages.checked)" @click="clickParent(main)"> 
                                            <label class="custom-control-label" :for="main[0].main">{{ main[0].main }}</label>
                                                <div class="form-check" v-for="pages of main">
                                                    <input type="checkbox" class="form-check-input" :id="pages.subpage" v-model="pages.checked"> 
                                                    <label class="form-check-label" :for="pages.subpage">{{ pages.subpage }}</label>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="active" name="active" v-model="active">
                            <input v-if="id_profile" type="hidden" id="id_utente" name="id_utente" :value="id_profile">
                            <input type="hidden" id="codiceSessione" name="codiceSessione" :value="codice_sessione">

                            <div class="float-sm-right ml-1 mb-1">
                                <button v-if="id_profile" type="submit" id="insert" class="btn btn-primary">Aggiorna</button>
                                <button v-else type="submit" id="update" class="btn btn-primary">Registra</button>
                            </div>
                            <div class="float-sm-right ml-1 mb-1">
                                <button type="submit" id="reset_form" class="btn btn-primary" :class=" [id_profile ? '' : 'd-none']" @click.prevent="reset_form">Indietro a nuovo utente</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                    <!-- loading -->
                    <div class="overlay dark" v-show="loading">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <!-- /.loading -->
                </div>
            </div>
        </div>`,
    data() {
        return {
            // variable to bind the form
            id_profile: null,
            description: null,
            active: 1,
            // variable to hold al pages and children pages
            mainPages: [],
            // variable to control the loading card
            loading: false
        }
    },
    methods: {
        insert_or_update_profile() {
            this.loading = true
            // set options to send with the post request
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    'action': 'insert_or_update_profile', 
                    'id_profile': this.id_profile,
                    'active': this.active,
                    'description': this.description,
                    'checked_pages': this.mainPages,
                    'codiceSessione': this.codice_sessione 
                })
            };
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json();
                    switch (data.code) {
                        case '500':
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message)
                            break;
                        case '406':
                            // reporting a forbidden request. ex: session code doesn't match
                            alert(data.message)
                            document.location.href = '../autenticazione_VueJs';
                            break;
                        case '401':
                            // reporting an unauthorized error. ex: profile already registered 
                            toastr.warning(data.message)
                            break;
                        case '400':
                            // reporting an unauthorized error. ex: profile already registered 
                            toastr.warning(data.message)
                            break;
                        case '201':
                            // reporting a success message. ex: profile inserted
                            toastr.success(data.message)
                            this.$emit('refresh_datatables')
                            this.reset_form()
                            break;
                        case '200':
                            // reporting a success message. ex: profile updated
                            toastr.success(data.message)
                            this.$emit('refresh_datatables')
                            this.reset_form()
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
        // get the profile data to update
        get_profile_data(id_profile) {
            this.loading = true
            this.reset_form();
            // set options to send with the post request
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'action': 'get_profile_data', 'id_profile': id_profile })
            };
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json();
                    switch (data.code) {
                        case '500':
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message)
                            break;
                        case '200':
                            // reporting a success message
                            toastr.success(data.message)
                            // set the value to the inputs
                            this.id_profile = id_profile
                            this.description = data.profile
                            data.pages.forEach(page => {
                                let mainPage = _.find(this.mainPages, { 'idPage': page.id_page });
                                mainPage.checked = Number(page.access) ? true : false
                            })
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
        // reset the from with enpty values
        reset_form() {
            this.id_profile = null,
            this.description = null,
            this.mainPages.forEach(pages => {
                pages.checked = false
            })
        },
        get_pages_to_checkbox() {
            this.loading = true
            // set options to send with the post request
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'action': 'pages_permission'})
            };
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json();
                    switch (data.code) {
                        case '500':
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message)
                            break;
                        case '200':
                            // reporting a success message
                            this.mainPages = data.pages
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
        // bind the parent checkbox with the children
        clickParent(main) {
            var pages = this.mainPages.filter(page => page.main == main[0].main)
            if (pages.find(page => !page.checked)) {
                pages.forEach(page => page.checked = true)
            } else {
                pages.forEach(page => page.checked = false)
            }
        },
    },
    computed: {
        pages() {
            return _.groupBy(this.mainPages, "main");
        },
    },
    mounted() {
        this.get_pages_to_checkbox()
    }
})
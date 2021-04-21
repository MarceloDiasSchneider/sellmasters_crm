app.component('profile', {
    props: {
        codice_sessione: {
            type: String
        }
    },
    template:
        /*html*/
        `<div class="col-md-12">
            <div class="card card-primary" :class="[ display ? '' : 'collapsed-card' ]">
                <div class="card-header">
                    <h3 class="card-title">Registra un nuovo profile</h3>
                    <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i :class="[ display ? 'fas fa-minus' : 'fas fa-plus' ]"></i>
                    </button>
                </div>
                </div>
                <div class="card-body" v-show="display">
                    <form action="#" id="description" @submit.prevent="insert_or_update_profile">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description">Profile descrizione</label>
                                    <input type="text" class="form-control" id="description" placeholder="ex: Finanziario" maxlength="20" required v-model="description">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="attivo" name="attivo" v-model="attivo">
                        <input v-if="id_profile" type="hidden" id="id_utente" name="id_utente" :value="id_profile">
                        <input type="hidden" id="codiceSessione" name="codiceSessione" :value="codice_sessione">

                        <button v-if="id_profile" type="submit" id="insert" class="btn btn-primary">Aggiorna</button>
                        <button v-else type="submit" id="update" class="btn btn-primary">Registra</button>
                    
                        <button type="submit" id="reset_form" class="btn btn-primary" :class=" [id_profile ? '' : 'd-none']" @click.prevent="reset_form">Indietro a nuovo utente</button>
                    </form>
                </div>
                <!-- /.card-body -->
                <!-- loading -->
                <div class="overlay dark" v-show="loading">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
                <!-- /.loading -->
            </div>
        </div>`,
    data() {
        return {
            // variable to bind the form
            id_profile: null,
            description: null,
            attivo: 1,
            // control the first card's display none  
            display: false,
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
                body: JSON.stringify({ 'action': 'insert_or_update_profile', 'id_profile': this.id_profile, 'description': this.description, 'codiceSessione': this.codice_sessione })
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
                            this.display = true
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
                this.description = null
        },

    }
})
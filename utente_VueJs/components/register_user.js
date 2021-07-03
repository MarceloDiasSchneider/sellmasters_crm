app.component('register_user', {
    props: {
        codice_sessione: {
            type: String
        },
        selected_row: {
            type: String
        }
    },
    template:
        /*html*/
        `<div class="modal fade" id="modal-xl">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h3 class="modal-title">Nuovo utente</h3>
                        <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <!-- /.modal-header -->
                    <div class="modal-body">
                        <form action='#' id='user_form' name='user_form' method='post' @submit.prevent="insert_or_update_user">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Nome">Nome</label>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" maxlength="30" v-model="nome" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Cognome">Cognome</label>
                                        <input type="text" class="form-control" id="cognome" name="cognome" placeholder="Cognome" maxlength="30" v-model="cognome">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="data-nascita">Data di nascita</label>
                                        <input type="date" class="form-control" id="data_nascita" name="data_nascita" placeholder="Data di nascita" v-model="data_nascita">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="codiceFiscale">Codice Fiscale</label>
                                        <input type="text" class="form-control" id="codice_fiscale" name="codice_fiscale" placeholder="Codice Fiscale" pattern="^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$" title="Codice fiscale errato." maxlength="16" v-model="codice_fiscale">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="telefono">Telefono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Telefono" maxlength="16" v-model="telefono">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>profile</label>
                                        <select id="profile" name="profile" class="form-control" v-model.number="profile" required>
                                            <option disabled selected value="0">seleziona un profile</option>
                                            <option v-for="option in select_option" :value="option.id">{{ option.description }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" maxlength="50" v-model="email" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" pattern="[a-zA-Z0-9]{8,20}" title="Deve contenere più di 8 caratteri" v-model="password" :required="!user_id">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="verificaPassword">Verifica password</label>
                                        <input type="password" class="form-control" id="verificaPassword" name="verificaPassword" placeholder="Verifica password" pattern="[a-zA-Z0-9]{8,20}" title="Deve contenere più di 8 caratteri" v-model="verificaPassword" :required="!user_id">
                                    </div>
                                </div>
                            </div>
                            <div class="float-right ml-1 mt-1">
                                <button type="submit" id="button" class="btn btn-primary">{{ button }}</button>
                            </div>
                        </form>
                    </div><!-- /.modal-body -->
                </div><!-- /.modal-content -->
            </div><!--/.modal-dialog -->
        </div>`,
    data() {
        return {
            // variable to bind the form
            nome: null,
            cognome: null,
            data_nascita: null,
            codice_fiscale: null,
            telefono: null,
            profile: 0,
            email: null,
            password: null,
            verificaPassword: null,
            attivo: 1,
            // variable to hold the user id to toggle between new user or update user
            user_id: null,
            // variable to hold the select options
            select_option: [],
            // variable to control the loading card
            loading: false
        }
    },
    methods: {
        // search all select option 
        select_options() {
            this.loading = true
            // set options to send with the post request
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({ 'action': 'get_profiles_active' })
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
                        case 200:
                            // format the data to show the selct options
                            data.profiles.forEach(profile => {
                                this.select_option.push({ id: profile.id_profile, description: profile.descrizione })
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
        // register a new user
        insert_or_update_user() {
            this.loading = true
            // check if required inputs was fielded
            if (this.nome != '' && this.profile != 0 && this.email != '' && (this.password != '' && this.verificaPassword != '' || this.user_id)) {
                // set options to send with the post request
                const requestOptions = {
                    method: 'POST',
                    mode: 'same-origin',
                    headers: { 'content-type': 'application/json' },
                    body: JSON.stringify({
                        'action': 'insert_or_update_user',
                        'nome': this.nome,
                        'cognome': this.cognome,
                        'data_nascita': this.data_nascita,
                        'codice_fiscale': this.codice_fiscale,
                        'telefono': this.telefono,
                        'profile': this.profile,
                        'email': this.email,
                        'password': this.password,
                        'verificaPassword': this.verificaPassword,
                        'attivo': this.attivo,
                        'codiceSessione': this.codice_sessione,
                        'user_id': this.user_id
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
                                this.emit_refresh_datatables()
                                this.emit_selected_row(null)
                                this.reset_form()
                                this.hide_modal()
                                break;
                            case 200:
                                // reporting a success message
                                toastr.success(data.message)
                                this.emit_refresh_datatables()
                                this.emit_selected_row(null)
                                this.reset_form()
                                this.hide_modal()
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
            } else {
                // create a report to each field that must to be completed
                alert('compila tutti i campi')
                this.loading = false
            }
        },
        // get the user data to update
        get_user_data() {
            this.loading = true
            // set options to send with the post request
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'action': 'get_user_data', 'id_utente': this.selected_row })
            };
            fetch('model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json();
                    switch (data.code) {
                        case 500:
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message)
                            break;
                        case 200:
                            // reporting a success message
                            toastr.success(data.message)
                            // set the value to the inputs
                            if (data.user.nome != undefined) { this.nome = data.user.nome } else { this.nome = null }
                            if (data.user.cognome != undefined) { this.cognome = data.user.cognome } else { this.cognome = null }
                            if (data.user.data_nascita != undefined) { this.data_nascita = data.user.data_nascita } else { this.data_nascita = null }
                            if (data.user.codice_fiscale != undefined) { this.codice_fiscale = data.user.codice_fiscale } else { this.codice_fiscale = null }
                            if (data.user.telefono != undefined) { this.telefono = data.user.telefono } else { this.telefono = null }
                            if (data.user.id_profile != undefined) { this.profile = data.user.id_profile } else { this.profile = null }
                            if (data.user.email != undefined) { this.email = data.user.email } else { this.email = null }
                            this.show_modal()
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
        // reset the from values to null
        reset_form() {
            this.loading = true
            this.user_id = false
            this.nome = null
            this.cognome = null
            this.data_nascita = null
            this.codice_fiscale = null
            this.telefono = null
            this.profile = 0
            this.email = null
            this.password = null
            this.verificaPassword = null
            this.loading = false
        },
        emit_refresh_datatables() {
            this.$emit('refresh_datatables')
        },
        emit_selected_row(data){
            this.$emit('set_selected_row', data)
        },
        show_modal(){
            $('#modal-xl').modal('show')
        },
        hide_modal(){
            $('#modal-xl').modal('hide')
        },
    },
    beforeMount() {
        this.select_options()
    },
    computed: {
        button(){
            if (this.user_id) {return 'Aggiorna'}
            return 'Registra'
        }
    },
    watch: { 
        selected_row: function(newVal) { 
            this.user_id = newVal
        }
    }
})
app.component('register_merchant', {
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
                        <h3 class="modal-title">Nuovo commerciante</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-xl">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="modal-body">
                        <!-- form start -->
                        <form action='#' id='merchant' name='merchant' method='post' @submit.prevent="insert_or_update_merchant">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nome">Nome</label>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" maxlength="45" required v-model="nome">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nome_sociale">Nome sociale</label>
                                        <input type="text" class="form-control" id="nome_sociale" name="nome_sociale" placeholder="Nome sociale" maxlength="100" v-model="nome_sociale">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="merchant_id">ID Commerciante</label>
                                        <input type="text" class="form-control" id="merchant_id" name="merchant_id" placeholder="ID Commerciante" maxlength="45" required v-model="merchant_id">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mws">MWS</label>
                                        <input type="text" class="form-control" id="mws" name="mws" placeholder="MWS" maxlength="450" v-model="mws">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="interval_between_check">Intervallo tra i controlli</label>
                                        <input type="number" class="form-control" id="interval_between_check" name="interval_between_check" placeholder="24 ore" v-model="interval_between_check">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nome_contatto">Nome contatto</label>
                                        <input type="text" class="form-control" id="nome_contatto" name="nome_contatto" placeholder="Nome contatto" maxlength="100" v-model="nome_contatto">
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
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" maxlength="100" v-model="email">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="indirizzo">Indirizzo</label>
                                        <input type="text" class="form-control" id="indirizzo" name="indirizzo" placeholder="Indirizzo" maxlength="100" v-model="indirizzo">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="numero_civico">Numero civico</label>
                                        <input type="text" class="form-control" id="numero_civico" name="numero_civico" placeholder="Numero civico" maxlength="45" v-model="numero_civico">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="citta">Citta</label>
                                        <input type="text" class="form-control" id="citta" name="citta" placeholder="Citta" maxlength="45" v-model="citta">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cap">Cap</label>
                                        <input type="text" class="form-control" id="cap" name="cap" placeholder="Cap" maxlength="45" v-model="cap">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="stato">Stato</label>
                                        <input type="text" class="form-control" id="stato" name="stato" placeholder="Stato" maxlength="45" v-model="stato">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="provincia">Provincia</label>
                                        <input type="text" class="form-control" id="provincia" name="provincia" placeholder="Provincia" maxlength="45" v-model="provincia">
                                    </div>
                                </div>
                            </div>
                            <div class="float-right ml-1 mt-1">
                                <button type="submit" id="button" class="btn btn-primary">{{ button }}</button>
                            </div>
                        </form>
                        <!-- /.form -->
                    </div>
                    <!-- /.card-body -->
                    <!-- loading -->
                    <div class="overlay dark" v-show="loading">
                        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                    </div>
                    <!-- /.loading -->
                </div>
                <!-- /.card -->
            </div>
        </div>`,
    data() {
        return {
            // variable to bind the form
            nome: null,
            nome_sociale: null,
            merchant_id: null,
            mws: null,
            interval_between_check: null,
            nome_contatto: null,
            telefono: null,
            email: null,
            indirizzo: null,
            numero_civico: null,
            citta: null,
            cap: null,
            stato: null,
            provincia: null,
            attivo: 1,
            // variable to hold the merchant id to toggle between new merchant or update merchant
            merchant_primary_id: null,
            // variable to control the loading card
            loading: false
        }
    },
    methods: {
        // register or update a merchant
        insert_or_update_merchant() {
            this.loading = true
            // check if required inputs was fielded
            if (this.nome != '' && this.merchant_id != '') {
                // set options to send with the post request
                const requestOptions = {
                    method: 'POST',
                    mode: 'same-origin',
                    headers: { 'content-type': 'application/json' },
                    body: JSON.stringify({
                        'action': 'insert_or_update_merchants',
                        'nome': this.nome,
                        'nome_sociale': this.nome_sociale,
                        'merchant_id': this.merchant_id,
                        'mws': this.mws,
                        'interval_between_check': this.interval_between_check,
                        'nome_contatto': this.nome_contatto,
                        'telefono': this.telefono,
                        'email': this.email,
                        'indirizzo': this.indirizzo,
                        'numero_civico': this.numero_civico,
                        'citta': this.citta,
                        'cap': this.cap,
                        'stato': this.stato,
                        'provincia': this.provincia,
                        'attivo': this.attivo,
                        'codiceSessione': this.codice_sessione,
                        'id': this.merchant_primary_id
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
                                console.log(data.message);
                                break;
                            case 406:
                                // reporting a forbidden request. ex: session code doesn't match
                                alert(data.message)
                                document.location.href = '../autenticazione_VueJs';
                                break;
                            case 401:
                                // reporting an unauthorized error. ex: merchant already registered
                                toastr.warning(data.message)
                                break;
                            case 201:
                                // show a success message. ex: merchant inserted
                                toastr.success(data.message)
                                this.emit_refresh_datatables()                                
                                this.emit_selected_row(null)
                                this.reset_form()
                                this.hide_modal()
                                break;
                            case 200:
                                // show a success message. ex: merchant updated
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
                alert('compila nome e merchant id')
            }
        },
        // get the merchants data to update
        get_merchant_data() {
            this.loading = true
            // set options to send with the post request
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 'action': 'get_merchant_data', 'id': this.selected_row })
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
                        case 400:
                            // report a bad request. ex: merchant not found
                            toastr.error(data.message)
                            break;
                        case 200:
                            // reporting a success message
                            toastr.success(data.message)
                            // set the value to the inputs
                            if (data.merchant.nome != undefined) { this.nome = data.merchant.nome } else { this.nome = null }
                            if (data.merchant.nome != undefined) { this.nome = data.merchant.nome } else { this.nome = null }
                            if (data.merchant.nome_sociale != undefined) { this.nome_sociale = data.merchant.nome_sociale } else { this.nome_sociale = null }
                            if (data.merchant.merchant_id != undefined) { this.merchant_id = data.merchant.merchant_id } else { this.merchant_id = null }
                            if (data.merchant.mws != undefined) { this.mws = data.merchant.mws } else { this.mws = null }
                            if (data.merchant.interval_between_check != undefined) { this.interval_between_check = data.merchant.interval_between_check } else { this.interval_between_check = null }
                            if (data.merchant.nome_contatto != undefined) { this.nome_contatto = data.merchant.nome_contatto } else { this.nome_contatto = null }
                            if (data.merchant.telefono != undefined) { this.telefono = data.merchant.telefono } else { this.telefono = null }
                            if (data.merchant.email != undefined) { this.email = data.merchant.email } else { this.email = null }
                            if (data.merchant.indirizzo != undefined) { this.indirizzo = data.merchant.indirizzo } else { this.indirizzo = null }
                            if (data.merchant.numero_civico != undefined) { this.numero_civico = data.merchant.numero_civico } else { this.numero_civico = null }
                            if (data.merchant.citta != undefined) { this.citta = data.merchant.citta } else { this.citta = null }
                            if (data.merchant.cap != undefined) { this.cap = data.merchant.cap } else { this.cap = null }
                            if (data.merchant.stato != undefined) { this.stato = data.merchant.stato } else { this.stato = null }
                            if (data.merchant.provincia != undefined) { this.provincia = data.merchant.provincia } else { this.provincia = null }
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
            this.nome = null,
            this.nome_sociale = null,
            this.merchant_id = null,
            this.mws = null,
            this.interval_between_check = null,
            this.nome_contatto = null,
            this.telefono = null,
            this.email = null,
            this.indirizzo = null,
            this.numero_civico = null,
            this.citta = null,
            this.cap = null,
            this.stato = null,
            this.provincia = null
            this.merchant_primary_id = null 
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
    computed: {
        button(){
            if (this.merchant_primary_id) {return 'Aggiorna'}
            return 'Registra'
        }
    },
    watch: { 
        selected_row: function(newVal) { 
            this.merchant_primary_id = newVal
        }
    }
})
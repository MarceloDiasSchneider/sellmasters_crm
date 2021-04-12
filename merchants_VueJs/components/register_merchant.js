app.component('register_merchant', {
    props: {

    },
    template:
        /*html*/
        `<div class="row">
            <div class="col-md-12">
                <!-- form registra utente -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Nuovo commerciante</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- form start -->
                        <form action='#' id='merchant' name='merchant' method='post' @submit.prevent="insert_or_update_merchant">
                            <div class="card-body">
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
                            </div>
                            <input type="hidden" id="attivo" name="attivo" value="1">
                            <input type="hidden" id="codiceSessione" name="codiceSessione" value="<?php echo $_SESSION['codiceSessione'] ?>">
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" id="register" class="btn btn-primary">Registra</button>
                                <button type="submit" id="back_register" class="btn btn-primary d-none">Indietro a nuovo commerciante</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!--/.form registra utente -->
        </div>`,
    data() {
        return {
            // variable to blind the form
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
            attivo: null,
            azione: null
        }
    },
    methods: {
        // register or update a merchant
        insert_or_update_merchant() {
            let refresh = false
            let datipresidalform = $("#merchant").serialize();
            $.ajax({
                type: "POST",
                url: "../merchants/model.php",
                data: "action=insert_or_update_merchants&" + datipresidalform,
                dataType: "json",
                async: false,
                success: function (data) {
                    switch (data.code) {
                        case '500':
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message);
                            break;
                        case '401':
                            // reporting an unauthorized error. ex: session code doesn't match 
                            alert(data.state)
                            console.log(data.message);
                            break;
                        case '409':
                            // reporting already inserted data. ex: nome and merchants already used
                            toastr.warning(data.message)
                            break;
                        case '201':
                            // show a success message. ex: merchant inserted
                            toastr.success(data.message)
                            // refresh the datatables
                            refresh = true
                            break;
                        case '200':
                            // show a success message. ex: merchant updated
                            toastr.success(data.message)
                            refresh = true
                            // refresh the datatables
                            break;
                        default:
                    }
                },
                error: function (msg) {
                    alert("Failed: " + msg.status + ": " + msg.statusText);
                }
            });
            // call a method to refresh the datatables
            if(refresh){ this.refresh_datatables()}
        },
        refresh_datatables(){
            $('#merchants').dataTable().api().ajax.reload(null, false);
        }
    }
})
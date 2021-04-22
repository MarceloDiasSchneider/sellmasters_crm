const app = Vue.createApp({
    data() {
        return {
            // variable from session
            codice_sessione: null,
            id_utente: null,
            nome: null,
            permissione: null,
            data: null,
            // variable to control the layout
            bodyClass: 'layout-fixed sidebar-mini-md sidebar-closed sidebar-collapse',
            nav_link: null,
        }
    },
    methods: {
        session() {
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({ 'action': 'get_session' })
            }
            fetch('../autenticazione_VueJs/model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json()
                    switch (data.code) {
                        case '204':
                            // show an aleter to the user and redirect to login page
                            document.location.href = data.url;
                            break;
                        case '200':
                            // set all data session to variables
                            this.codice_sessione = data.codiceSessione
                            this.id_utente = data.id_utente
                            this.nome = data.nome
                            // this.permissione = data.permissione
                            this.data = data.data
                            break;
                        default:
                            break;
                    }
                })
                // report an error if there is
                .catch(error => {
                    this.errorMessage = error;
                    console.log('There was an error!', error);
                });
        },
        set_page_active(page){
            this.nav_link = page
        }
    },
    beforeMount() {
        this.session()
    }
})
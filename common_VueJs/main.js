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
            menu_pages: {},
            page_data: {},
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
                        case 500:
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            console.log(data.message)
                            break;
                        case 406:
                            // redirect the page to new authentication
                            document.location.href = data.url;
                            break;
                        case 200:
                            // set all data session to variables
                            this.codice_sessione = data.codiceSessione
                            this.id_utente = data.id_utente
                            this.nome = data.nome
                            this.data = data.data
                            this.menu_pages = _.groupBy(data.accessPages , "main");
                            break;
                        default:
                            // redirect the page to new authentication
                            document.location.href = '../autenticazione_VueJs';
                            break;
                    }
                })
                // report an error if there is
                .catch(error => {
                    this.errorMessage = error;
                    console.log('There was an error!', error);
                });
        },
        set_page_active(main ,subpage){
            this.page_data = {'main': main, 'subpage': subpage}
        },
    },
    beforeMount() {
        this.session()
    }
})
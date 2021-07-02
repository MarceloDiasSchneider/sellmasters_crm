app.component('forgot-password', {
    props: {
        forgot_password: {
            type: Boolean,
            required: true
        }
    },
    template:
        /*html*/
        `<div class="card card-outline card-primary" v-show="forgot_password">
            <div class="card-header text-center">
                <h1 class="h1">Sell Masters</h1>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Hai dimenticato la password? Qui puoi recuperare facilmente una nuova password.</p>
                <form action="recover-password.html" id='forgot-password' name='forgot-password' method="post" @submit.prevent="forgot_password_clicked">
                    <div class="input-group mb-3">
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" v-model="email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="feedback text-red mb-3">
                        <span id="feedback">{{ message }}</span>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" id="submit" class="btn btn-primary btn-block">Recupera password</button>
                        </div>
                    </div>
                </form>
                <p class="mt-3 mb-1">
                    <a href="#" @click.prevent="login_clicked">Accedi</a>
                </p>
            </div><!-- /.card-body -->
            <div class="overlay dark" v-show="loading">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>`,
    data() {
        return {
            loading: false,
            message: null,
            email: null
        }
    },
    methods: {
        // request the forgot password to the backend
        forgot_password_clicked() {
            this.loading = true
            // get all data from the form 
            const requestOptions = {
                method: 'POST',
                mode: 'same-origin',
                headers: { 'content-type': 'application/json' },
                body: JSON.stringify({
                    'action': 'forgot_password',
                    'email': this.email,
                })
            }
            fetch('../autenticazione_VueJs/model.php', requestOptions)
                // process the backend response
                .then(async response => {
                    const data = await response.json()
                    switch (data.code) {
                        case 500:
                            // reporting an internal server error. ex: try catch
                            alert(data.state)
                            this.message = null
                            console.log(data.message)
                            break;
                        case 401:
                            // reporting an unauthorized error. ex: email not found
                            this.message = data.message
                            break;
                        case 200:
                            // reporting a success message. ex: check your email to chande the password
                            toastr.success(data.message)
                            this.message = null
                            this.email = null
                        default:
                            break;
                        }
                    this.loading = false
                })
                // report an error if there is
                .catch(error => {
                    this.errorMessage = error;
                    console.log('There was an error!', error);
                });
        },
        // go to the login page
        login_clicked() {
            this.$emit('login-page')
        }
    },
})
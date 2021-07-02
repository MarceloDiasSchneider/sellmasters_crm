app.component('login', {
    props: {
        login: {
            type: Boolean,
            required: true
        }
    },
    template: 
        /*html*/
		`<div class="card card-outline card-primary" v-show="login">
			<div class="card-header text-center">
				<h1 class="h1">Sell Masters</h1>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Accedi per iniziare la tua sessione</p>

				<form id="authentication" name="authentication" action="#" method="post" @submit.prevent="authentication">
					<div class="input-group mb-3">
						<input type="email" id="email" name="email" class="form-control" placeholder="Email" v-model="email" required>
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input :type="password_type" id="password" name="password" class="form-control" placeholder="Password" required pattern="[a-zA-Z0-9]{8,20}" title="Deve contenere più di 8 caratteri" v-model="password">
						<div class="input-group-append">
							<div class="input-group-text" @click="show_password">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="feedback text-red mb-3">
						<span id="feedback">{{ message }}</span>
					</div>
					<div class="row">
						<div class="col-12 mb-3">
							<button type="submit" id="submit" class="btn btn-primary btn-block">Accedi</button>
						</div>
					</div>
				</form>
				<p class="mb-1">
					<a href="#" @click.prevent="forgot_password_clicked">Ho dimenticato la mia password</a>
				</p>
			</div><!-- /.card-body -->
			<div class="overlay dark" v-show="loading">
				<i class="fas fa-2x fa-sync-alt fa-spin"></i>
			</div>
		</div>`,
    data() {
        return {
			loading: false,
            // variable to control the layout
			message: '',
			password_type: 'password',
			// variable to bind the form
			email: null,
			password: null
        }
    },
    methods: {
		// request the login to the backend
		authentication() {
			this.loading = true 
			// get all data from the form 
			const requestOptions = {
				method: 'POST',
				mode: 'same-origin',
				headers: { 'content-type': 'application/json' },
				body: JSON.stringify({
					'action': 'autenticazione',
					'email': this.email,
					'password': this.password
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
							console.log(data.message)
							break;
						case 406:
							// reporting an Not Acceptable error 
							alert(data.state)
							console.log(data.message)
							break;
						case 401:
							// reporting an unauthorized error. ex: email or password doesn't match 
							this.message = data.message
							break;
						case 201:
							// go to the dashboard home page setted on the backend
							document.location.href = `../${data.url}`;
							break;
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
		// go to the forgot password page
		forgot_password_clicked() {
			this.$emit('forgot-password-page')
		},
		show_password() {
			if (this.password_type == 'password') {
				this.password_type = 'text'
			} else {
				this.password_type = 'password'
			}
		}
	}
})
app.component('password-recovery', {
	props: {
		password_recovery: {
			type: Boolean,
			required: true
		},
		email: {
			type: String,
			required: true
		},
		code: {
			type: String,
			required: true
		}
	},
	template:
		/*html*/
		`<div class="login-box" v-show="password_recovery">
			<div class="card card-outline card-primary">
				<div class="card-header text-center">
					<h1>Sell Masters</h1>
				</div>
				<div class="card-body">
					<p class="login-box-msg">Sei solo a un passo dalla tua nuova password, recuperala ora.</p>
					
					<form action="#" id="recover-password" name="recover-password" method="post" @submit.prevent="recover_password_clicked">
						<div class="input-group mb-3">
							<input :type="password_type" id="password" name="password" class="form-control" placeholder="Password" pattern="[a-zA-Z0-9]{8,20}" title="Deve contenere più di 8 caratteri" v-model="password" required>
							<div class="input-group-append">
								<div class="input-group-text" @click="show_password">
									<span class="fas fa-lock"></span>
								</div>
							</div>
						</div>
						<div class="input-group mb-3">
							<input :type="password_type" id="confirm-password" name="confirm-password" class="form-control" placeholder="Conferma Password" pattern="[a-zA-Z0-9]{8,20}" title="Deve contenere più di 8 caratteri"  v-model="confirm_password" required>
							<div class="input-group-append">
								<div class="input-group-text" @click="show_password">
									<span class="fas fa-lock"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<button type="submit" class="btn btn-primary btn-block">Cambiare la password</button>
							</div>
						</div>
						<input type="hidden" id="email" name="email" v-model="email">
						<input type="hidden" id="code" name="code" v-model="code">
					</form>

					<p class="mt-3 mb-1">
						<a href="#" @click.prevent="login_clicked">Accedi</a>
					</p>
				</div>
				<!-- /.login-card-body -->
			</div>
			<!-- /.card -->
		</div>`,
	data() {
		return {
			password_type: 'password',
			message: null,
			password: null,
			confirm_password: null
		}
	},
	methods: {
		// request the recovery password to the backend
		recover_password_clicked() {
			// get all data from the form 
			const requestOptions = {
				method: 'POST',
				mode: 'same-origin',
				headers: { 'content-type': 'application/json' },
				body: JSON.stringify({
					'action': 'recover_password',
					'email': this.email,
					'code': this.code,
					'password': this.password,
					'confirm-password': this.confirm_password
				})
			}
			fetch('model.php', requestOptions)
				// process the backend response
				.then(async response => {
					const data = await response.json()
					switch (data.code) {
						case '500':
							// reporting an internal server error. ex: try catch
							alert(data.state)
							console.log(data.message)
							break;
						case '401':
							// reporting an unauthorized error. ex: code to change the passord is not valid
							toastr.warning(data.message)
							break;
						case '400':
							// reporting a bad request error. ex: the password dosen't match
							toastr.warning(data.message)
							break;
						case '200':
							// reporting a success message. ex: the password is updated
							toastr.success(data.message)
							// wait 2 second to go to the login page
							setTimeout(function () {
								login_clicked()
							}, 2000);
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
		// go to the login page
		login_clicked() {
			this.$emit('login-page')
		},
		// show and hidden the passowrd
		show_password() {
			if (this.password_type == 'password') {
				this.password_type = 'text'
			} else {
				this.password_type = 'password'
			}
		}
	}
})
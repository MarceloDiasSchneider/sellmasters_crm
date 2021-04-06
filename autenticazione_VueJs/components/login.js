app.component('login', {
	props: {
		login: {
			type: Boolean,
			required: true
		}
	},
	template:
		/*html*/
		`<!-- pagina originale pages/examples/login-v2.html -->
		<div class="login-box" v-show="login">
			<!-- /.login-logo -->
			<div class="card card-outline card-primary">
				<div class="card-header text-center">
					<h1 class="h1">Sell Masters</h1>
				</div>
				<div class="card-body">
					<p class="login-box-msg">Accedi per iniziare la tua sessione</p>
					<form id="authentication" name="authentication" action="#" method="post">
						<div class="input-group mb-3">
							<input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-envelope"></span>
								</div>
							</div>
						</div>
						<div class="input-group mb-3">
							<input type="password" id="password" name="password" class="form-control" placeholder="Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno un numero e una lettera maiuscola e minuscola e almeno 8 o più caratteri">
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-lock"></span>
								</div>
							</div>
						</div>
						<div class="feedback text-red mb-3">
							<span id="feedback">{{ message }}</span>
						</div>
						<div class="row">
							<div class="col-12 mb-3">
								<button type="submit" id="submit" class="btn btn-primary btn-block" @click.prevent="authentication">Accedi</button>
							</div>
							<!-- /.col -->
						</div>
					</form>
					<p class="mb-1">
						<a href="#" @click.prevent="forgot_password_clicked">Ho dimenticato la mia password</a>
					</p>
				</div>
				<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>
		<!-- /.login-box -->`,
	data(){
		return {
			message: ''
		}
	},
	methods: {
		// request the login to the backend
		authentication() {
			let dataForm = $("#authentication").serialize();
			let message
			$.ajax({
				type: "POST",
				url: "../autenticazione/model.php",
				data: "action=autenticazione&" + dataForm,
				dataType: "json",
				async: false,
				success: function (data) {
					switch (data.code) {
						case '500':
							// reporting an internal server error. ex: try catch
							alert(data.state)
							console.log(data.message)
							break;
						case '401':
							// reporting an unauthorized error. ex: email or password doesn't match 
							console.log(data.message)
							message = data.message
							break;
						case '201':
							// go to the dashboard home page setted on the backend
							document.location.href = `${data.url}_VueJs`;
							break;
						default:
					}
				},
				error: function (msg) {
					alert("Failed: " + msg.status + ": " + msg.statusText);
				}
			});
			// show the message to user
			this.message = message
		},
		// go to the forgot password page
		forgot_password_clicked() {
			this.$emit('forgot-password-page')
		}
	}
})

app.component('password-recovery', {
	props: {
		password_recovery:{
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
	`<!-- pagina originale pages/examples/recover-password-v2.html -->
	<!-- /.login-card-body -->
	<div class="login-box" v-show="password_recovery">
		<div class="card card-outline card-primary">
			<div class="card-header text-center">
				<h1>Sell Masters</h1>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Sei solo a un passo dalla tua nuova password, recuperala ora.</p>
				<form action="#" id="recover-password" name="recover-password" method="post">
					<div class="input-group mb-3">
						<input type="password" id="password" name="password" class="form-control" placeholder="Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno un numero e una lettera maiuscola e minuscola e almeno 8 o più caratteri">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" id="confirm-password" name="confirm-password" class="form-control" placeholder="Conferma Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Deve contenere almeno un numero e una lettera maiuscola e minuscola e almeno 8 o più caratteri">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<button type="submit" class="btn btn-primary btn-block" @click.prevent="recover_password_clicked">Cambiare la password</button>
						</div>
						<!-- /.col -->
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
	</div>
	<!-- /.login-box -->`,
	methods: {
		// request the recovery password to the backend
		recover_password_clicked(){
			let dataForm = $("#recover-password").serialize();
			console.log(dataForm);
			$.ajax({
				type: "POST",
				url: "../autenticazione/model.php",
				data: "action=recover_password&" + dataForm,
				dataType: "json",
				async: false,
				success: function (data) {
					if(data.state == 'bad request'){
						toastr.warning(data.message)
					} else if (data.state == 'success') {
						toastr.success(data.message)
						// set a time to redirect the user to login page
						setTimeout(function(){ 
							window.location.href = "index.html";
						}, 2000);	
						url.search = ''
					} else if (data.state == 'error'){
						alert('Problema, trove più tarde')
						console.log(data.message);
					} else if(data.state == 'unauthorized'){
						toastr.error(data.message)
					}
				},
				error: function (msg) {
					alert("Failed: " + msg.status + ": " + msg.statusText);
				}
			})
		},
		// go to the login page
		login_clicked() {
			this.$emit('login-page')
		}
	}
})
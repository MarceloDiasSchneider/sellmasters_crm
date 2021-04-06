app.component('forgot-password', {
	props: {
		forgot_password: {
			type: Boolean,
			required: true
		}
	},
	template:
		/*html*/
		`<!-- pagina originale pages/examples/forgot-password-v2.html -->
		<!-- login-box -->
		<div class="login-box" v-show="forgot_password">
			<div class="card card-outline card-primary">
				<div class="card-header text-center">
					<h1 class="h1">Sell Masters</h1>
				</div>
				<div class="card-body">
					<p class="login-box-msg">Hai dimenticato la password? Qui puoi recuperare facilmente una nuova password.</p>
					<form action="#" id='forgot-password' name='forgot-password' method="post">
						<div class="input-group mb-3">
							<input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
							<div class="input-group-append">
								<div class="input-group-text">
									<span class="fas fa-envelope"></span>
								</div>
							</div>
						</div>
						<div class="feedback text-red mb-3">
							<span id="feedback"></span>
						</div>
						<div class="row">
							<div class="col-12">
								<button type="submit" id="submit" class="btn btn-primary btn-block" @click.prevent="forgot_password_clicked">Recupera password</button>
							</div>
							<!-- /.col -->
						</div>
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
		// request the forgot password to the backend
		forgot_password_clicked() {
			let dataForm = $("#forgot-password").serialize();
			$.ajax({
				type: "POST",
				url: "../autenticazione/model.php",
				data: "action=forgot_password&" + dataForm,
				dataType: "json",
				async: false,
				success: function (data) {
					if (data.state == 'success') {
						toastr.success(data.message)
						$('#email').val('')
						$('#feedback').text('')
					} else if (data.state == 'error') {
						alert('Problema')
					} else if (data.state == 'unauthorized') {
						$('#feedback').text(data.message)
					} else if (data.state == 'Internal server error') {
						toastr.error(data.message)
					}
				},
				error: function (msg) {
					alert("Failed: " + msg.status + ": " + msg.statusText);
				}
			});
		},
		// go to the login page
		login_clicked() {
			this.$emit('login-page')
		}
	},
	computed: {

	}
})
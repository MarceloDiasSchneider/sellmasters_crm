// Set the main vue js
const app = Vue.createApp({
	data() {
		return {
			// variable to nav through the pages
			login: true,
			forgot_password: false,
			password_recovery: false,
			// variable to update a new password
			email: '',
			code: ''
		}
	},
	methods: {
		// set the variables to show the login page
		loginPage() {
			this.login = true
			this.forgot_password = false
			this.password_recovery = false
		},
		// set the variables to show the forgot password page
		forgotPasswordPage() {
			this.login = false
			this.forgot_password = true
			this.password_recovery = false
		},
		// set the variables to show the password recovery page
		passwordRecoveryPage(){
			this.login = false
			this.forgot_password = false
			this.password_recovery = true
		},
		// check if the url has a email and code show hte recovery password page
		check_url() {
			const queryString = window.location.search;
			if(queryString != ''){
				const urlParams = new URLSearchParams(queryString);
				this.email = urlParams.get('email')
				this.code = urlParams.get('code')
				this.passwordRecoveryPage()
			}
		}
	},
	computed: {

	},
	// call methods before the mount app
	beforeMount() {
		this.check_url()
	}
})

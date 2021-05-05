<!-- Import commons files -->
<?php include_once('../common_VueJs/header.html'); ?>
<!-- Import aditionals files -->
<?php include_once('style_sheet.html'); ?>

<body class="hold-transition login-page">
	<div id="app" class="login-box">
		<login :login="login" @forgot-password-page="forgotPasswordPage"></login>
		<forgot-password :forgot_password="forgot_password" @login-page="loginPage"></forgot-password>
		<password-recovery :password_recovery="password_recovery" :email="email" :code="code" @login-page="loginPage"></password-recovery>
	</div>

	<!-- Import commons files -->
	<?php include_once('../common_VueJs/required_script.html'); ?>
	<!-- Import aditionals files -->
	<?php include_once('required_script.html'); ?>
</body>

</html>
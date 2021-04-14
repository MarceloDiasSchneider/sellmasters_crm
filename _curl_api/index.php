<!-- Import commons files -->
<?php include_once('../common_VueJs/header.html'); ?>
<!-- Import aditionals files -->
<?php include_once('style_sheet.html'); ?>

<body class="hold-transition sidebar-mini">
	<div id="app">
		<navbar></navbar>
		<sidebar :nome="nome"></sidebar>
		<content_wrapper :codice_sessione="codice_sessione" :nome="nome"></content_wrapper>
		<footer_bottom></footer_bottom>
	</div>

	<!-- Import commons files -->
	<?php include_once('../common_VueJs/required_script.html'); ?>
	<!-- Import aditionals files -->
	<?php include_once('required_script.html'); ?>
</body>

</html>
<!-- Import commons files -->
<?php include_once('../common_VueJs/header.html'); ?>
<!-- Import aditionals files -->
<?php include_once('style_sheet.html'); ?>

<body class="layout-fixed sidebar-mini-md sidebar-closed sidebar-collapse">
	<div id="app">
		<navbar></navbar>
		<sidebar :nome="nome" :nav_link="nav_link"></sidebar>
		<content_wrapper :codice_sessione="codice_sessione" @page="set_page_active"></content_wrapper>
		<footer_bottom></footer_bottom>
	</div>

	<!-- Import commons files -->
	<?php include_once('../common_VueJs/required_script.html'); ?>
	<!-- Import aditionals files -->
	<?php include_once('required_script.html'); ?>
</body>

</html>
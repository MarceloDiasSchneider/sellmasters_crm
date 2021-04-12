<?php include_once('../common_VueJs/header.html'); ?>
<?php include_once('style_sheet.html'); ?>

<body class="hold-transition sidebar-mini">
    <div id="app">
		<navbar></navbar>
		<sidebar :nome="nome"></sidebar>
		<content_wrapper></content_wrapper>
		<footer_bottom></footer_bottom>
	</div>
    <?php include_once('../common_VueJs/required_script.html'); ?>
    <?php include_once('required_script.html'); ?>
</body>

</html>
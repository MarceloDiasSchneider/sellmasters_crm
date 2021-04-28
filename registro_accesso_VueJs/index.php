<?php include_once('../common_VueJs/header.html'); ?>
<?php include_once('style_sheet.html'); ?>

<body class="control-sidebar-slide-open layout-fixed sidebar-collapse">
    <div id="app">
		<navbar></navbar>
		<sidebar :nome="nome" :nav_link="nav_link"></sidebar>
		<content_wrapper @page="set_page_active"></content_wrapper>
		<footer_bottom></footer_bottom>
	</div>
    <?php include_once('../common_VueJs/required_script.html'); ?>
    <?php include_once('required_script.html'); ?>
</body>

</html>
app.component('sidebar', {
	props: {
		nome: {
			type: String,
			requided: true
		},
		page_data: {
			type: Object,
			requided: true
		},
		menu_pages: {
			type: Object,
			requided: true
		}
	},
	template:
		/*html*/
		`<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<!-- Brand Logo -->
			<a href="index3.html" class="brand-link">
				<img src="../AdminLte/dist/img/sellmastersLogo.png" alt="AdminLTE Logo"
					class="brand-image img-circle elevation-3" style="opacity: .8">
				<span class="brand-text font-weight-light">Sell Masters</span>
			</a>

			<!-- Sidebar -->
			<div class="sidebar">
				<!-- Sidebar user panel (optional) -->
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="../AdminLte/dist/img/user1-128x128.jpg" class="img-circle elevation-2" alt="User Image">
					</div>
					<div class="info">
						<a href="#" class="d-block">
							{{ nome }}
						</a> 
					</div>
				</div>

				<!-- SidebarSearch Form -->
				<div class="form-inline">
					<div class="input-group" data-widget="sidebar-search">
						<input class="form-control form-control-sidebar" type="search" placeholder="Search"
							aria-label="Search">
						<div class="input-group-append">
							<button class="btn btn-sidebar">
								<i class="fas fa-search fa-fw"></i>
							</button>
						</div>
					</div>
				</div>

				<!-- Sidebar Menu -->
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-legacy" data-widget="treeview" role="menu" data-accordion="false">
						<!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
						<li class="nav-item" :class="[page_data.main == main[0].main ? 'menu-is-opening menu-open' : '' ]" v-for="main in menu_pages">
							<a href="#" class="nav-link" :class="[page_data.main == main[0].main ? 'active' : '' ]">
								<i :class="main[0].nav_icon" :id="main[0].subpage"></i> 
								<p>
									{{ main[0].main }}
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>
							<ul class="nav nav-treeview" :style="[page_data.main == main[0].main ? 'display: block;' : 'display: none;']" v-for="subpage of main">
								<li class="nav-item" :id="subpage.subpage">
									<a :href="'../' + subpage.link" class="nav-link" :class="[page_data.subpage == subpage.link ? 'active ' : '' ]">
										<i :class="[page_data.subpage == subpage.link ? 'far fa-dot-circle nav-icon text-lightblue' : 'far fa-circle nav-icon' ]" :for="subpage.subpage">
										</i>
										<p :for="subpage.subpage">{{ subpage.subpage }}</p>
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</nav>
				<!-- /.sidebar-menu -->
			</div>
			<!-- /.sidebar -->
		</aside>`,
})
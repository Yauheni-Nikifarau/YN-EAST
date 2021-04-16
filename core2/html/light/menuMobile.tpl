<div id="preloader" style="display:none">
	<div class="lock-screen"></div>
	<div class="block">
		<div class="spinner-icon"></div>
		<div class="">_tr(Загрузка)...</div>
	</div>
</div>
<table id="main" width="100%"><tbody><tr>
		<td id="menu-container" valign="top">
			<div id="menu-wrapper">
				<div id="heme-button">
					<a href="index.php#module=admin&action=welcome" title="<!--SYSTEM_NAME-->" class="site-name"
					   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=admin&action=welcome');"><!--SYSTEM_NAME--></a>
				</div>
				<ul class="nav" id="menu-modules">
					<!-- BEGIN modules -->
					<li id="module-[MODULE_ID]" class="menu-module">
						<a href="index.php#module=[MODULE_ID][MODULE_ACTION]"
						   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=[MODULE_ID][MODULE_ACTION]');"
						>[MODULE_NAME]</a>
					</li>
					<!-- END modules -->
				</ul>
			</div>

			<ul id="menu-submodules">
				<!-- BEGIN submodules -->
				<li id="submodule-[MODULE_ID]-[SUBMODULE_ID]" class="menu-submodule" style="display:none" >
					<a href="index.php#module=[MODULE_ID]&action=[SUBMODULE_ID]"
					   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=[MODULE_ID]&action=[SUBMODULE_ID]');">[SUBMODULE_NAME]</a>
				</li>
				<!-- END submodules -->
			</ul>
		</td>
		<td id="main-content" valign="top">
			<nav class="navbar navbar-default navbar-static-top" id="navbar-top">
				<div class="navbar-header">
					<a id="sidebar-toggle" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
					<div class="module-title"></div>
					<div class="module-action"></div>
				</div>
				<ul id="user-section" class="nav navbar-top-links navbar-right">
					<li class="dropdown">
						<div class="dropdown-toggle" data-toggle="dropdown">
							<div class="avatar-container">
								<img src="[GRAVATAR_URL]?&s=28&d=mm" alt=""/>
							</div>
							<i class="fa fa-caret-down"></i>
						</div>
						<ul class="dropdown-menu dropdown-menu-right dropdown-user">
							<li class="dropdown-user-login">
								<b><!--CURRENT_USER_FN--> <!--CURRENT_USER_LN--></b><br>
								<!--CURRENT_USER_LOGIN-->
							</li>
							<li class="divider"></li>

							<li class="dropdown-profile profile">
								<a href="index.php#module=profile">
									<i class="fa fa-user"></i>
									_tr(Профиль)
								</a>
							</li>
							<li class="dropdown-profile messages">
								<a href="index.php#module=profile&action=messages">
									<i class="fa fa-envelope-o"></i>
									_tr(Сообщения)
								</a>
							</li>
							<li class="dropdown-profile divider"></li>
							<li class="dropdown-billing">
								<a href="index.php#module=billing">
									<i class="fa fa-credit-card"></i>
									_tr(Ваш баланс)
								</a>
							</li>
							<li class="dropdown-billing divider"></li>
							<li class="dropdown-settings">
								<a href="index.php#module=settings">
									<i class="fa fa-cogs"></i>
									_tr(Настройки)
								</a>
							</li>
							<li class="dropdown-settings divider"></li>
							<li>
								<a href="javascript:void(0);" onclick="logout()">
									<i class="fa fa-power-off"></i>
									_tr(Выход)
								</a>
							</li>
						</ul>
					</li>
				</ul>
			</nav>
			<div id="mainContainer">
				<div id="main_body"></div>
			</div>
		</td>
	</tr></tbody></table>

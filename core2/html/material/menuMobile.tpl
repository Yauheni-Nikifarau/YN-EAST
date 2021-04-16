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
				<div id="home-button">
					<a href="index.php#module=admin&action=welcome" title="<!--SYSTEM_NAME-->" class="site-name"
					   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=admin&action=welcome');">
						<span><i class="fa fa-home"></i> <!--SYSTEM_NAME--></span>
					</a>
				</div>
				<ul class="nav" id="menu-modules">
					<!-- BEGIN modules -->
					<li id="module-[MODULE_ID]" class="menu-module">
						<a href="index.php#module=[MODULE_ID][MODULE_ACTION]"
						   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=[MODULE_ID][MODULE_ACTION]');"
						><span class="module-title">[MODULE_NAME]</span></a>
						<div class="module-submodules" style="display: none"></div>
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
			<div class="swipe-area"></div>
			<nav class="navbar navbar-default navbar-static-top" id="navbar-top">
				<div class="navbar-header">
					<a id="sidebar-toggle" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
					<div class="module-title"></div>
					<div class="module-action"></div>
				</div>
				<ul id="user-section" class="nav navbar-top-links navbar-right">
					<li class="nav navbar-nav nav-profile dropdown">
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
								<a href="index.php#module=profile"
								   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=profile');"></a>
							</li>
							<li class="dropdown-profile messages">
								<a href="index.php#module=profile&action=messages"
								   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=profile&action=messages');">
									<i class="fa fa-envelope-o"></i>
									_tr(Сообщения)
								</a>
							</li>
							<li class="dropdown-profile divider"></li>
							<li class="dropdown-billing">
								<a href="index.php#module=billing"
								   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=billing');"></a>
							</li>
							<li class="dropdown-billing divider"></li>
							<li class="dropdown-settings">
								<a href="index.php#module=settings"
								   onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=settings');"></a>
							</li>
							<li class="dropdown-settings divider"></li>
							<li>
								<a href="javascript:logout()">
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

<div id="preloader" style="display:none">
    <div class="lock-screen"></div>
    <div class="block">
        <div class="spinner-icon"></div>
        <div class="spinner-text">_tr(Загрузка)...</div>
    </div>
</div>
<div id="main">
    <div id="menu-container">
        <div id="menu-wrapper">
            <a id="heme-button" href="index.php#module=admin&action=welcome" title="<!--SYSTEM_NAME-->" class="site-name"
               onclick="if (event.button === 0 && ! event.ctrlKey) load('index.php#module=admin&action=welcome');"><!--SYSTEM_NAME--></a>
            <ul id="user-section" class="nav navbar-top-links navbar-right">
                <!-- BEGIN navigate_item -->
                <li class="nav navbar-nav nav-[MODULE_NAME]">[HTML]</li>
                <!-- END navigate_item -->

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
                                _tr(Ваши платежи)
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
    </div>
    <div id="main-content">
        <div id="mainContainer">
            <div id="main_body"></div>
        </div>
    </div>
</div>

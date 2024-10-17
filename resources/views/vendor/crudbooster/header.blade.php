

	<style>
		.modal-content {
			-webkit-border-radius: 10px !important;
			-moz-border-radius: 10px !important;
			border-radius: 10px !important; 
		}
		.modal-header{
			-webkit-border-radius: 10px 10px 0px 0px !important;
			-moz-border-radius: 10px 10px 0px 0px !important;
			border-radius: 10px 10px 0px 0px !important; 
		}
		#passwordStrengthBar {
			display: flex;
			justify-content: space-between;
			width: 100%;
		}

		.progress-bar {
			width: 32%;
			height: 8px;
			background-color: lightgray;
			transition: background-color 0.3s;
			border-radius: 5px;
		}

		#bar1.active {
			background-color: #dd4b39 ; /* Weak */
		}

		#bar2.active {
			background-color: #f39c12 ; /* Strong */
		}

		#bar3.active {
			background-color: #00a65a; /* Excellent */
		}
		#textUppercase.active {
			color: #00a65a; /* Excellent */
		}
		#textLength.active {
			color: #00a65a; /* Excellent */
		}
		#textNumber.active {
			color: #00a65a; /* Excellent */
		}
		#textChar.active {
			color: #00a65a; /* Excellent */
		}
	</style>

<!-- Main Header -->
<header class="main-header">
    <!-- Logo -->
    <a href="{{url(config('crudbooster.ADMIN_PATH'))}}" title='{{Session::get('appname')}}' class="logo">{{CRUDBooster::getSetting('appname')}}</a>
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title='Notifications' aria-expanded="false">
                        <i id='icon_notification' class="fa fa-bell-o"></i>
                        <span id='notification_count' class="label label-danger" style="display:none">0</span>
                    </a>
                    <ul id='list_notifications' class="dropdown-menu">
                        <li class="header">{{cbLang("text_no_notification")}}</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;">
                                <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">
                                    <li>
                                        <a href="#">
                                            <em>{{cbLang("text_no_notification")}}</em>
                                        </a>
                                    </li>

                                </ul>
                                <div class="slimScrollBar"
                                     style="width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 195.122px; background: rgb(0, 0, 0);"></div>
                                <div class="slimScrollRail"
                                     style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div>
                            </div>
                        </li>
                        <li class="footer"><a href="{{route('NotificationsControllerGetIndex')}}">{{cbLang("text_view_all_notification")}}</a></li>
                    </ul>
                </li>

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ CRUDBooster::myPhoto() }}" class="user-image" alt="User Image"/>
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ CRUDBooster::myName() }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ CRUDBooster::myPhoto() }}" class="img-circle" alt="User Image"/>
                            <p>
                                {{ CRUDBooster::myName() }}
                                <small>{{ CRUDBooster::myPrivilegeName() }}</small>
                                <small><em><?php echo date('d F Y')?></em></small>
                            </p>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-{{ cbLang('left') }}">
                                <a title='Profile' href="{{ route('AdminCmsUsersControllerGetProfile') }}" class="btn btn-default btn-border"><i
                                            class='fa fa-user'></i> </a>
                            </div>
                            <div class="pull-{{ cbLang('left') }}" style="padding-left:3px">
                                <a title='Change password' href="{{ route('change-password') }}" id="btnChangePass" class="btn btn-warning btn-border"><i
                                            class='fa fa-key'></i></a>
                            </div>
                            <div class="pull-{{ cbLang('left') }}" style="padding-left:3px; font-style: italic !important">
                                <a title='Updates' href="{{ route('announcement') }}" class="btn btn-info btn-border"><i
                                            class='fa fa-info-circle' style="font-style: italic !important"></i></a>
                            </div>
                            <div class="pull-{{ cbLang('right') }}">
                                <a title='Lock Screen' href="{{ route('getLockScreen') }}" class='btn btn-default btn-border'><i class='fa fa-lock'></i></a>
                                <a href="javascript:void(0)" onclick="swal({
                                        title: '{{cbLang('alert_want_to_logout')}}',
                                        type:'info',
                                        showCancelButton:true,
                                        allowOutsideClick:true,
                                        confirmButtonColor: '#DD6B55',
                                        confirmButtonText: '{{cbLang('button_logout')}}',
                                        cancelButtonText: '{{cbLang('button_cancel')}}',
                                        closeOnConfirm: false
                                        }, function(){
                                        location.href = '{{ route("getLogout") }}';

                                        });" title="{{cbLang('button_logout')}}" class="btn btn-danger btn-border"><i class='fa fa-power-off'></i></a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>


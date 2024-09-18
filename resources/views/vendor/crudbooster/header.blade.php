

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
                                <a href="{{ route('AdminCmsUsersControllerGetProfile') }}" class="btn btn-default btn-flat"><i
                                            class='fa fa-user'></i> {{cbLang("label_button_profile")}}</a>
                            </div>
                            <div class="pull-{{ cbLang('left') }}" style="padding-left:3px">
                                <a title='Change password' id="btnChangePass" class="btn btn-warning btn-flat"><i
                                            class='fa fa-key'></i></a>
                            </div>
                            <div class="pull-{{ cbLang('right') }}">
                                <a title='Lock Screen' href="{{ route('getLockScreen') }}" class='btn btn-default btn-flat'><i class='fa fa-lock'></i></a>
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

                                        });" title="{{cbLang('button_logout')}}" class="btn btn-danger btn-flat"><i class='fa fa-power-off'></i></a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>

{{-- Modal --}}
<div class="modal fade" id="cp-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header btn-danger" style="text-center;">
                <h4 class="modal-title" id="pass_qwerty"><b> <i class="fa fa-lock"></i> Change password</b></h4>
            </div>
        
            <form method="POST" action="{{ route('update_password') }}" id="cpChangePasswordForm">
                @csrf
                <input type="hidden" value="{{Session::get('admin_id')}}" name="user_id">
                <div class="modal-body">
                        @if(Session::get('message_type') == "danger_exist")
                            <span class="text-center" style="color: #dd4b39; font-size: 16px; font-weight:bold; font-style:italic"> Password already used! please use another password.</span>
                        @endif
                    <div class="form-group">
                        <label for="cp_current_password">Current Password</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-lock"></span>
                            </div>
                            <input type="password" class="form-control inputs" id="cp_current_password" name="cp_current_password" placeholder="Current password" required>
                        </div>
                        <i class="fa fa-eye" id="toggleCurrentPassword" style="cursor: pointer; position: absolute; right: 25px; top: 50px; z-index: 10000"></i>
                    </div>

                    <div class="form-group">
                        <label for="cp_new_password">New Password</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-lock"></span>
                            </div>
                            <input type="password" class="form-control inputs match_pass" id="cp_new_password" name="cp_new_password" placeholder="New password" required>
                        </div>
                        <i class="fa fa-eye" id="toggleNewPassword" style="cursor: pointer; position: absolute; right: 25px; top: 124px; z-index: 10000"></i>
                        <!-- Password strength progress bar -->
                        <div id="passwordStrengthBar" style="margin-top: 10px;">
                            <div class="progress-bar" id="bar1"></div>
                            <div class="progress-bar" id="bar2"></div>
                            <div class="progress-bar" id="bar3"></div>
                        </div>
                        <!-- Password strength progress bar -->
                        <div style="margin-top: 10px;">
                            <div class="progress-text" id="textUppercase" style="font-size: 15px"> <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Atleast 1 Uppercase</span></div>
                            <div class="progress-text" id="textLength" style="font-size: 15px"> <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Atleast 8 length</span></div>
                            <div class="progress-text" id="textNumber" style="font-size: 15px"> <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Atleast Contain a Number</span></div>
                            <div class="progress-text" id="textChar" style="font-size: 15px"> <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Atleast Contain a Special Character</span></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cp_confirm_password">Confirm Password</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-lock"></span>
                            </div>
                            <input type="password" class="form-control inputs match_pass" id="cp_confirm_password" name="cp_confirm_password" placeholder="Confirm password" required>
                        </div>
                        <i class="fa fa-eye" id="toggleConfirmPassword" style="cursor: pointer; position: absolute; right: 25px; top: 310px; z-index: 10000"></i>
                        <span id="pass_not_match" style="display: none; color:red; font-size:15px">Password not match!</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" id="btnCancel"><i class="fa fa-times-circle"></i> Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnCpSubmit"><i class="fa fa-key"></i> Change password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('bottom')
    <script type="text/javascript">
        $('#btnChangePass').click(function(){
            $('#cp-modal').modal('show');
        });
        $('#btnCancel').click(function(){
            $('#cp-modal').modal('hide');
        });
        $(document).ready(function() {
            const admin_path = "{{CRUDBooster::adminPath()}}"
			const msg_type = "{{ session('message_type') }}";
			if (msg_type == 'success'){
                setTimeout(function(){
                    location.assign(admin_path+'/logout');
				}, 2000);
			}
            
            $('#btnCpSubmit').attr('disabled',true);
			// Toggle for current password
			$('#toggleCurrentPassword').on('click', function() {
				let currentPassword = $('#cp_current_password');
				let type = currentPassword.attr('type') === 'password' ? 'text' : 'password';
				currentPassword.attr('type', type);
				$(this).toggleClass('fa-eye fa-eye-slash');
			});

			// Toggle for new password
			$('#toggleNewPassword').on('click', function() {
				let newPassword = $('#cp_new_password');
				let type = newPassword.attr('type') === 'password' ? 'text' : 'password';
				newPassword.attr('type', type);
				$(this).toggleClass('fa-eye fa-eye-slash');
			});

			// Toggle for confirm password
			$('#toggleConfirmPassword').on('click', function() {
				let confirmPassword = $('#cp_confirm_password');
				let type = confirmPassword.attr('type') === 'password' ? 'text' : 'password';
				confirmPassword.attr('type', type);
				$(this).toggleClass('fa-eye fa-eye-slash');
			});

			$(document).on('input', '#cp_new_password, #cp_current_password, #cp_confirm_password', function() {
				validateInputs();
			});

			//CHANGE PASS
			$("#btnCpSubmit").click(function(event) {
				event.preventDefault();
				$.ajaxSetup({
					headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					url: "{{ route('check-current-password') }}",
					dataType: "json",
					type: "POST",
					data: {
						"password": $('#cp_current_password').val(),
						"id": '{{session()->get("admin_id")}}'
					},
					success: function (data) {
						console.log(data.items);
					  if(data.items === 0){
						swal({
							type: 'error',
							title: 'Current password invalid!',
							icon: 'error',
							confirmButtonColor: "#367fa9",
						}); 
						event.preventDefault();
						return false;
					  } else{
							$("#cpChangePasswordForm").submit();       
							$('#btnCpSubmit').attr('disabled',true);         
					  }
						
					}
				});
			                                                
			});

        });
        // Function to check password strength
        function checkPasswordStrength(password) {
            // Check if password has at least 8 characters, and contains uppercase, lowercase, digit, and special character
            const hasUpperCase = /[A-Z]/.test(password); // Uppercase letter
            const hasLowerCase = /[a-z]/.test(password); // Lowercase letter
            const hasNumber = /\d/.test(password); // Digit
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>;]/.test(password); 

            // Password length check and classification based on conditions
            if (password.length < 6 && password.length !== 0) {
                return 'Weak';
            } else if (password.length >= 6 && password.length < 8 && hasLowerCase && hasNumber) {
                return 'Strong';
            } else if (password.length >= 8 && hasUpperCase && hasLowerCase && hasNumber && hasSpecialChar) {
                return 'Excellent';
            } else if (password.length >= 8 && hasLowerCase && hasNumber) {
                return 'Strong'; // Handle cases where it is strong but not excellent
            } else{
                return 'Weak';
            }
        }

        //Function to check text active
        function checkPasswordTextActive(password){
            const hasUpperCase = /[A-Z]/.test(password); // Uppercase letter
            const hasLowerCase = /[a-z]/.test(password); // Lowercase letter
            const hasNumber = /\d/.test(password); // Digit
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>;]/.test(password); 
            const allCharacters = []; // Array to store password strength criteria

            if (hasUpperCase) {
                allCharacters.push('Uppercase');
            }
            if (password.length >= 8) {
                allCharacters.push('Length');
            }
            if (hasNumber) {
                allCharacters.push('Number');
            }
            if (hasSpecialChar) {
                allCharacters.push('Character');
            }
            return allCharacters;
        }

        function validateInputs(){
            const inputs = $('.inputs').get();
            let isDisabled = true;
            let password = $('#cp_new_password').val();
            
            //BARS
            let strength = checkPasswordStrength(password);
            // Reset bars
            $('#bar1, #bar2, #bar3').removeClass('active');
            // Activate bars based on password strength
            if (strength === 'Weak') {
                $('#bar1').addClass('active');
                isDisabled = false;
            } else if (strength === 'Strong') {
                $('#bar1, #bar2').addClass('active');
                isDisabled = false;
            } else if (strength === 'Excellent') {
                $('#bar1, #bar2, #bar3').addClass('active');
                $('#text1, #text2, #text3').addClass('active');
                isDisabled = true;
            }
            
            //TEXT
            let textActive = checkPasswordTextActive(password);
            const textActiveMap = {
                'Uppercase': '#textUppercase',
                'Length': '#textLength',
                'Number': '#textNumber',
                'Character': '#textChar'
            };
            
            // First, remove 'active' class from all selectors in textActiveMap
            Object.values(textActiveMap).forEach(function(selector) {
                $(selector).removeClass('active');
            });

            // Then, iterate through the textActive array and add 'active' class to corresponding selectors
            textActive.forEach(function(value) {
                const selector = textActiveMap[value];
                if (selector) {
                    $(selector).addClass('active');
                }
            });

            const new_pass = $('#cp_new_password').val();
            const confirm_pass = $('#cp_confirm_password').val();
            if(new_pass && confirm_pass){
                if(new_pass != confirm_pass){
                    isDisabled = false;
                    $('.match_pass').css('border', '2px solid red');
                    $('#pass_not_match').show();
                }else{
                    $('.match_pass').css('border', '');
                    $('#pass_not_match').hide();
                }
            }

            inputs.forEach(input =>{
                const currentVal = $(input).val(); 
                if(!currentVal){
                    isDisabled = false;
                }
            });
            console.log(isDisabled);
            $('#btnCpSubmit').attr('disabled',!isDisabled);
        }
    </script>
@endpush

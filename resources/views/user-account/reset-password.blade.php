<!DOCTYPE html>
<html>
<head>
    <title>Digits Asset Management System</title>
</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset ('css/font-family.css') }}">
<link rel="stylesheet" type="text/css" href="{{asset('vendor/crudbooster/assets/sweetalert/dist/sweetalert.css')}}">
<link href="{{asset("vendor/crudbooster/assets/adminlte/font-awesome/css")}}/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="{{asset('datetimepicker/bootstrap-datetimepicker.min.css')}}">
<link rel='stylesheet' href='{{asset("vendor/crudbooster/assets/css/main.css") }}'/>
    <style type="text/css">
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,300&display=swap");
        body{
            background: {{ CRUDBooster::getSetting("login_background_color")?:'#dddddd'}} url('{{ CRUDBooster::getSetting("login_background_image")?asset(CRUDBooster::getSetting("login_background_image")):asset('vendor/crudbooster/assets/bg_blur3.jpg') }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            font-family:poppins !important;
        }
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
        
        .card{
            width:90% !important; 
            margin:auto !important;
            margin-top: 30% !important;
        }
        
        @media (min-width:729px){
           .card{
                width:30% !important; 
                margin:auto !important;
                margin-top: 8% !important;
           }
        }

      
    </style>
<body>
    <div class="card">
        <div class='card-header' style="background-color: #3c8dbc; color: #fff; font-weight:bold; font-size:20px; text-align:center">
            <span class="text-bdo">DRS PASSWORD RESET FORM</span>
         </div> 
        <div class="card-body">
            <form method="POST" action="{{ route('postResetPassword') }}" id="changePasswordForm">
                @csrf
                <input type="hidden" value="{{$email}}" name="email">
                <div class="form-group">
                    <label for="new_password" style="font-size:15px; font-weight:bold">New Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fa fa-lock"></span>
                            </div>
                          </div>
                        <input type="password" class="form-control inputs match_pass" id="new_password" name="new_password" placeholder="New password" required>
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fa fa-eye" id="toggleNewPassword"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password strength progress bar -->
                    <div id="passwordStrengthBar" style="margin-top: 10px;">
                        <div class="progress-bar" id="bar1"></div>
                        <div class="progress-bar" id="bar2"></div>
                        <div class="progress-bar" id="bar3"></div>
                    </div>
                    <!-- Password strength text -->
                    <div style="margin-top: 10px;">
                        <div class="progress-text" id="textUppercase" style="font-size: 15px">
                            <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Must include at least one uppercase letter.</span>
                        </div>
                        <div class="progress-text" id="textLength" style="font-size: 15px">
                            <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Minimum length of 8 characters.</span>
                        </div>
                        <div class="progress-text" id="textNumber" style="font-size: 15px">
                            <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Must contain at least one number.</span>
                        </div>
                        <div class="progress-text" id="textChar" style="font-size: 15px">
                            <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Must include at least one special character.</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password" style="font-size:15px; font-weight:bold">Confirm Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <span class="fa fa-lock"></span>
                            </div>
                        </div>
                        <input type="password" class="form-control inputs match_pass" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fa fa-eye" id="toggleConfirmPassword"></i>
                            </div>
                        </div>
                    </div>
                    <span id="pass_not_match" style="display: none; color:red; font-size:15px">Password does not match!</span>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-primary pull-right" id="btnSubmit"><i class="fa fa-key"></i> Change password</button>
        </div>
    </div>
</body>
</html>

<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/jQueryUI/jquery-ui.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/select2/select2.min.js') }}"></script>
<script src="{{asset('vendor/crudbooster/assets/sweetalert/dist/sweetalert.min.js')}}"></script>
<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/daterangepicker-bs3.css') }}">

<link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css"/>

<!-- Bootstrap time Picker -->
<link rel="stylesheet" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/timepicker/bootstrap-timepicker.min.css') }}">
<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script>
    $(document).ready(function() {
        const admin_path = "{{CRUDBooster::adminPath()}}"
        const msg_type = "{{ session('message_type') }}";
        if (msg_type == 'success'){
            setTimeout(function(){
                location.assign(admin_path+'/logout');
            }, 2000);
        }

        $('#btnSubmit').attr('disabled',true);
        // Toggle for current password
        $('#toggleCurrentPassword').on('click', function() {
            let currentPassword = $('#current_password');
            let type = currentPassword.attr('type') === 'password' ? 'text' : 'password';
            currentPassword.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Toggle for new password
        $('#toggleNewPassword').on('click', function() {
            let newPassword = $('#new_password');
            let type = newPassword.attr('type') === 'password' ? 'text' : 'password';
            newPassword.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Toggle for confirm password
        $('#toggleConfirmPassword').on('click', function() {
            let confirmPassword = $('#confirm_password');
            let type = confirmPassword.attr('type') === 'password' ? 'text' : 'password';
            confirmPassword.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        $(document).on('input', '#new_password, #current_password, #confirm_password', function() {
            validateInputs();
        });

        //CHANGE PASS
        $("#btnSubmit").click(function(event) {
            event.preventDefault();
            $('#btnSubmit').attr('disabled',true);   
            $.ajax({
                url: "{{ route('postResetPassword') }}",
                dataType: "json",
                type: "POST",
                data: $('#changePasswordForm').serialize(),
                success: function (data) {
                    if (data.status === 'success') {
                        swal({
                            type: data.status,
                            title: data.message,
                            icon: 'success',
                            confirmButtonColor: "#359D9D",
                        },function(){
                            window.location.replace(data.redirect);
                        });
                        
                    }  else if (data.status === 'error') {
                        swal({
                            type: data.status,
                            title: data.message,
                            icon: 'error',
                            confirmButtonColor: "#359D9D",
                        },function(){
                            window.location.replace(data.redirect);
                       });
                        
                    }  else {
                        swal({
                            type: 'error',
                            title: data.message,
                            icon: 'error',
                            confirmButtonColor: "#359D9D",
                        },function(){
                            $('#changePasswordForm').trigger("reset");
                            location.reload();
                        });
                        
                    }  
                     
                }
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
            let password = $('#new_password').val();
            
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

            const new_pass = $('#new_password').val();
            const confirm_pass = $('#confirm_password').val();
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

            $('#btnSubmit').attr('disabled',!isDisabled);
        }
    });
</script>
    


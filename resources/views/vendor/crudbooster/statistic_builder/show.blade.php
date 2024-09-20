@extends('crudbooster::admin_template')
@section('content')
@push('head')
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
@endpush
    @include('crudbooster::statistic_builder.index')
	<div class="modal fade" id="tos-modal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header btn-danger" style="text-center; font-size:14px">
					<h4 class="modal-title" id="pass_qwerty"><b> <i class="fa fa-lock"></i> System detect password using qwerty, Please change your password!</b></h4>
					<h4 class="modal-title" id="pass_old"><b> <i class="fa fa-lock"></i> <span class="label label-danger">Your password is out of date, Please change it!</span> </b></h4>
				</div>
			
				<form method="POST" action="{{ route('update_password') }}" id="changePasswordForm">
					@csrf
					<input type="hidden" value="{{Session::get('admin_id')}}" name="user_id">
					<input type="hidden" id="type" name="type">
					<div class="modal-body">
							@if(Session::get('message_type') == "danger")
								<span class="text-center" style="color: #dd4b39; font-size: 16px; font-weight:bold; font-style:italic"> Password already used! please use another password.</span>
							@endif
						<div class="form-group">
							<label for="current_password">Current Password</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-lock"></span>
								</div>
								<input type="password" class="form-control inputs" id="current_password" name="current_password" placeholder="Current password" required>
							</div>
							<i class="fa fa-eye" id="toggleCurrentPassword" style="cursor: pointer; position: absolute; right: 25px; top: 50px; z-index: 10000"></i>
						</div>
	
						<div class="form-group">
							<label for="new_password">New Password</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-lock"></span>
								</div>
								<input type="password" class="form-control inputs match_pass" id="new_password" name="new_password" placeholder="New password" required>
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
								<div class="progress-text" id="textUppercase" style="font-size: 15px"> <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Must include at least one uppercase letter.</span></div>
								<div class="progress-text" id="textLength" style="font-size: 15px"> <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Minimum length of 8 characters.</span></div>
								<div class="progress-text" id="textNumber" style="font-size: 15px"> <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Must contain at least one number.</span></div>
								<div class="progress-text" id="textChar" style="font-size: 15px"> <i class="fa fa-check-circle"></i> <span style="font-style: italic"> Must include at least one special character.</span></div>
							</div>
						</div>
	
						<div class="form-group">
							<label for="confirm_password">Confirm Password</label>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-lock"></span>
								</div>
								<input type="password" class="form-control inputs match_pass" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
							</div>
							<i class="fa fa-eye" id="toggleConfirmPassword" style="cursor: pointer; position: absolute; right: 25px; top: 310px; z-index: 10000"></i>
							<span id="pass_not_match" style="display: none; color:red; font-size:15px">Password not match!</span>
						</div>
					</div>
	
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" id="btnWaive"><i class="fa fa-refresh"></i> Waive</button>
						<button type="button" class="btn btn-danger" id="btnSubmit"><i class="fa fa-key"></i> Change password</button>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection
@push('bottom')
    <script type="text/javascript">
        $(window).on('load',function(){
            @if (Hash::check('qwerty',Session::get('admin_password')))
                $('#tos-modal').modal('show');
				$('#pass_old').hide();
				$('#btnWaive').hide();
			@elseif (Hash::check('qwerty1234',Session::get('admin_password')))
                $('#tos-modal').modal('show');
				$('#pass_old').hide();
			@elseif(Session::get('password_is_old'))
				$('#tos-modal').modal('show');
				$('#pass_old').show();
				$('#pass_qwerty').hide();
            @endif     
        });

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
						"password": $('#current_password').val(),
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
							$('#type').val(1);
							$("#changePasswordForm").submit();       
							$('#btnSubmit').attr('disabled',true);         
					  }
						
					}
				});
			                                                
			});

			//WAIVE
			$("#btnWaive").click(function(event) {
				event.preventDefault();
				$.ajaxSetup({
					headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					url: "{{ route('check-waive-count') }}",
					dataType: "json",
					type: "POST",
					data: {
						"password": $('#current_password').val(),
						"id": '{{session()->get("admin_id")}}'
					},
					success: function (data) {
						console.log(data.items);
					  if(data.items === 0){
						swal({
							type: 'error',
							title: 'You cannot waive more than 3 times!',
							icon: 'error',
							confirmButtonColor: "#367fa9",
						}); 
						event.preventDefault();
						return false;
					  } else{
							$('#type').val(0);
							$("#changePasswordForm").submit();       
							$('#btnWaive').attr('disabled',true);         
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
@endpush 
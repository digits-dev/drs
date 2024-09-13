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
			width: 30%;
			height: 5px;
			background-color: lightgray;
			margin-right: 5px;
			transition: background-color 0.3s;
		}

		#bar1.active {
			background-color: red; /* Weak */
		}

		#bar2.active {
			background-color: orange; /* Strong */
		}

		#bar3.active {
			background-color: green; /* Excellent */
		}
	</style>
@endpush
    @include('crudbooster::statistic_builder.index')
	<div class="modal fade" id="tos-modal" role="dialog" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header btn-primary" style="text-center">
					<h4 class="modal-title"><b> <i class="fa fa-lock"></i> Please change your password!</b></h4>
				</div>
				<form action="POST" action="{{ route('update_password') }}" id="changePasswordForm">
					<div class="modal-body">
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
							<i class="fa fa-eye" id="toggleNewPassword" style="cursor: pointer; position: absolute; right: 25px; top: 125px; z-index: 10000"></i>
							   <!-- Password strength progress bar -->
							   <div id="passwordStrengthBar" style="margin-top: 10px;">
									<div class="progress-bar" id="bar1"></div>
									<div class="progress-bar" id="bar2"></div>
									<div class="progress-bar" id="bar3"></div>
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
							<i class="fa fa-eye" id="toggleConfirmPassword" style="cursor: pointer; position: absolute; right: 25px; top: 214px; z-index: 10000"></i>
							<span id="pass_not_match" style="display: none; color:red; font-size:15px">Password not match!</span>
						</div>
					</div>
	
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="btnSubmit"><i class="fa fa-key"></i> Change password</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	
@endsection
@push('bottom')
    <script type="text/javascript">
        // $(window).on('load',function(){
        //     @if (Hash::check('qwerty',Session::get('admin_password')))
        //         $('#tos-modal').modal('show');
        //     @endif     
        // });

		$(document).ready(function() {
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

			// Password strength validation and loading bar
			$('#new_password, #confirm_password, #current_password').on('input', function() {
				let password = $(this).val();
				let strength = checkPasswordStrength(password);
				// Reset bars
				$('#bar1, #bar2, #bar3').removeClass('active');

				// Activate bars based on password strength
				if (strength === 'Weak') {
					$('#bar1').addClass('active');
					$('#btnSubmit').attr('disabled',true);
				} else if (strength === 'Strong') {
					$('#bar1, #bar2').addClass('active');
					$('#btnSubmit').attr('disabled',true);
				} else if (strength === 'Excellent' && validateInputs()) {
					$('#bar1, #bar2, #bar3').addClass('active');
				}
			});

			// $(document).on('input', '#current_password, #new_password, #confirm_password', function() {
			// 	validateInputs();
			// });

			// $(document).on('input', '#confirm_password', function() {
			// 	confirmPassword();
			// });

			$('#btnSubmit').on('click', function(event) {
				$("#changePasswordForm").submit();                                                   
			});

			// Function to check password strength
			function checkPasswordStrength(password) {
				// Check if password has at least 8 characters, and contains uppercase, lowercase, digit, and special character
				const hasUpperCase = /[A-Z]/.test(password); // Uppercase letter
				const hasLowerCase = /[a-z]/.test(password); // Lowercase letter
				const hasNumber = /\d/.test(password); // Digit
				const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>;]/.test(password); 

				// Password length check and classification based on conditions
				if (password.length < 6) {
					return 'Weak';
				} else if (password.length >= 6 && password.length < 8 && hasLowerCase && hasNumber) {
					return 'Strong';
				} else if (password.length >= 8 && hasUpperCase && hasLowerCase && hasNumber && hasSpecialChar) {
					return 'Excellent';
				} else if (password.length >= 8 && hasLowerCase && hasNumber) {
					return 'Strong'; // Handle cases where it is strong but not excellent
				} else {
					return 'Weak'; // Return weak if none of the conditions above are met
				}
			}

			function confirmPassword(){
				let isDisabled = true;
				const new_pass = $('#new_password').val();
				const confirm_pass = $('#confirm_password').val();
				if(new_pass != confirm_pass){
					isDisabled = false;
					$('.match_pass').css('border', '2px solid red');
					$('#pass_not_match').show();
				}else{
					$('.match_pass').css('border', '');
					$('#pass_not_match').hide();
				}
				
				$('#btnSubmit').attr('disabled',!isDisabled);
			}

			function validateInputs(){
				const inputs = $('.inputs').get();
				let isDisabled = true;
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
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{cbLang("page_title_login")}} : {{Session::get('appname')}}</title>
    <meta name='generator' content='CRUDBooster'/>
    <meta name='robots' content='noindex,nofollow'/>
    <link rel="shortcut icon"
          href="{{ CRUDBooster::getSetting('favicon')?asset(CRUDBooster::getSetting('favicon')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}">

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{asset('vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{asset('vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css"/>

    <!-- support rtl-->
    @if (in_array(App::getLocale(), ['ar', 'fa']))
        <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">
        <link href="{{ asset("vendor/crudbooster/assets/rtl.css")}}" rel="stylesheet" type="text/css"/>
@endif

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <link rel='stylesheet' href='{{asset("vendor/crudbooster/assets/css/main.css")}}'/>
    <style type="text/css">
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,300&display=swap");
        .login-page, .register-page {
            background: {{ CRUDBooster::getSetting("login_background_color")?:'#dddddd'}} url('{{ CRUDBooster::getSetting("login_background_image")?asset(CRUDBooster::getSetting("login_background_image")):asset('vendor/crudbooster/assets/bg_blur3.jpg') }}');
            color: {{ CRUDBooster::getSetting("login_font_color")?:'#ffffff' }}  !important;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            font-family:poppins;
        }

        .login-box, .register-box {
            margin: 2% auto;
           
        }

        .login-box-body {
            box-shadow: 0px 0px 50px rgba(0, 0, 0, 0.8);
            background: rgba(255, 255, 255, 0.9);
            color: {{ CRUDBooster::getSetting("login_font_color")?:'#666666' }}  !important;
        }

        html, body {
            overflow: hidden;
        }
        .login-box-msg{
            font-weight: bold;
            font-size: 16px;
        }
        
    </style>
</head>

<body class="login-page">

<div class="login-box">
    <div class="login-logo">
        <a href="{{url('/')}}">
            <img title='{!!(Session::get('appname') == 'CRUDBooster')?"<b>CRUD</b>Booster":CRUDBooster::getSetting('appname')!!}'
                 src='{{ CRUDBooster::getSetting("logo")?asset(CRUDBooster::getSetting('logo')):asset('vendor/crudbooster/assets/b14235f79a26445a7bf4cc5bfb0e0854.png') }}'
                 style='max-width: 100%;max-height:170px'/>
        </a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        @if (Session::get('message') != '' )
            @if(Session::get('message_type') === 'danger')
                <div class='alert alert-danger'>
                    <i class="fa fa-warning"></i> {{ Session::get('message') }}
                </div>
            @else
                <div class='alert alert-success'>
                    <i class="fa fa-check-circle"></i> {{ Session::get('message') }}
                </div>
            @endif
        @endif

        <p class='login-box-msg'>{{cbLang("login_message")}}</p>
        <form autocomplete='off' action="{{ route('postLogin') }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            
            @if(!empty(config('services.google')))

                <div style="margin-bottom:10px" class='row'>
                    <div class='col-xs-12'>

                        <a href='{{route("redirect", 'google')}}' class="btn btn-primary btn-block btn-flat"><i class='fa fa-google'></i>
                            Google Login</a>

                        <hr>
                    </div>
                </div>
            @endif
            
            <div class="form-group has-feedback">
                <div class="input-group">
                    <div class="input-group-addon">
                        <div class="input-group-text">
                            <span class="glyphicon glyphicon-envelope"></span>
                        </div>
                    </div>
                    <input autocomplete='off' type="text" class="form-control" name='email' required placeholder="Email"/>
                </div>
            </div>
            <div class="form-group has-feedback">
                <div class="input-group">
                    <div class="input-group-addon">
                        <div class="input-group-text">
                            <span class="glyphicon glyphicon-lock"></span>
                        </div>
                    </div>
                    <input autocomplete='off' type="password" class="form-control" name='password' id="password" required placeholder="Password"/>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-eye-open" id="togglePassword"></span>
                    </div>
                </div>
            </div>
            <div style="margin-bottom:10px" class='row'>
                <div class='col-xs-12'>
                    <button type="submit" class="btn btn-primary btn-block btn-flat"><i class='fa fa-lock'></i> {{cbLang("button_sign_in")}}</button>
                </div>
            </div>

            <div class='row'>
                <div class='col-xs-12' align="center"><p style="padding:10px 0px 10px 0px">{{cbLang("text_forgot_password")}} <a
                                href='{{route("getForgot")}}'>{{cbLang("click_here")}}</a></p></div>
            </div>
            
        </form>


        <br/>
        <!--a href="#">I forgot my password</a-->

    </div><!-- /.login-box-body -->

</div><!-- /.login-box -->


<!-- jQuery 2.2.3 -->
<script src="{{asset('vendor/crudbooster/assets/adminlte/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
<!-- Bootstrap 3.4.1 JS -->
<script src="{{asset('vendor/crudbooster/assets/adminlte/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
</body>
</html>

<script>
    $('#togglePassword').on('click', function() {
        let password = $('#password');
        let type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).toggleClass('glyphicon-eye-open glyphicon-eye-close');
    });
</script>
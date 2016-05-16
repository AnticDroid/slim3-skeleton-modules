@extends('layouts.portal')

@section('content')
    <div class="col-sm-6 col-sm-offset-3 sso-login">
        <div class="row">
        	<div class="col-xs-12">
                <div class="well">
                    <form method="post" action="/session">
                        <div class="form-group">
                            <span class="sr-only">{{ translate('username') }}</span>
                            <input type="text" class="form-control" name="email" placeholder="{{ translate('username') }}" value="{{ @$params['email'] }}">
                        </div>

                        <div class="form-group" style="position: relative;">
                            <span class="sr-only">{{ translate('password') }}</span>
                            <input  type="password" class="form-control" name="password" placeholder="{{ translate('password') }}" value="">
                            <a style="position: absolute; top: 10px; right: 20px;" href="/accounts/resetpassword">{{ translate('forgot_password') }}</a>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-lg btn-primary btn-block btn-login" type="submit">
                                    {{ translate('login_button') }}
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <a href="/accounts" class="btn btn-lg btn-default btn-block btn-signup">
                                    {{ translate('register_button') }}
                                </a>
                            </div>
                        </div>

                        <div class="row sso-loginOr">
                            <div class="col-xs-12">
                                <hr class="sso-hrOr">
                                <span class="sso-spanOr">or</span>
                            </div>
                        </div>

                        <button type="submit" formaction="/session/facebook" class="btn btn-lg btn-block btn-facebook">
                            <i class="fa fa-facebook"></i>&nbsp;&nbsp;
                            {{ translate('signin_with_facebook') }}
                        </button>

                        <div class="row">
                    		<div class="col-xs-12">
                    			<hr>
                    		</div>
                    	</div>

                        <div class="row">
                        	<div class="col-xs-12">
                        		<label><input type="checkbox" name="remember_me" value="1" checked="checked"> {{ translate('remember_me') }}</label>
                        	</div>
                        </div>

                        @if(isset($params['returnTo']))
                            <input type="hidden" name="returnTo" value="{{ $params['returnTo'] }}">
                        @endif
            		</form>
            	</div>
        	</div>
        </div>
    </div>
@stop

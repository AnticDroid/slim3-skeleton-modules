@extends('layouts.portal')

@section('content')
    <div class="col-sm-8 col-sm-offset-2 sso-register">
        <div class="row">
        	<div class="col-xs-12">
                <div class="well">
                    <form method="post" action="/accounts/resetpassword" id="passwordRecoveryEmailForm">
                        <div class="form-group">
                            <label for="email">{{ translate('email') }}</label>
                            <input type="text" class="form-control" name="email" placeholder="{{ translate('email') }}" value="{{ @$params['email'] }}">
                        </div>

                        <input type="text" class="more_info" name="more_info" value="" style="position: absolute; left: -9999px">

                        <hr>

                        <div>
                            <button type="submit" class="btn btn-primary">{{ translate('send_recovery_email_button') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

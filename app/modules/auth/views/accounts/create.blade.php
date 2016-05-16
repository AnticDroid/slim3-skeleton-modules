@extends('layouts.portal')

@section('content')
<div class="col-sm-6 col-sm-offset-3 sso-register">
    <div class="row">
    	<div class="col-xs-12">
            <div class="well">
                <form method="post" action="/accounts">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name" class="sr-only">{{ translate('first_name') }}</label>
                                <input type="text" class="form-control" name="first_name" placeholder="{{ translate('first_name') }}" value="{{ @$params['first_name'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name" class="sr-only">{{ translate('last_name') }}</label>
                                <input type="text" class="form-control" name="last_name" placeholder="{{ translate('last_name') }}" value="{{ @$params['last_name'] }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="sr-only">{{ translate('email') }}</label>
                        <input type="text" class="form-control" name="email" placeholder="{{ translate('email') }}" value="{{ @$params['email'] }}">
                    </div>

                    <div class="form-group">
                        <label for="password" class="sr-only">{{ translate('password') }}</label>
                        <input type="password" class="form-control" name="password" placeholder="{{ translate('password') }}" value="">
                    </div>

                    <label class="checkbox-inline agreement">
                        <input name="agreement" type="checkbox"> {!! translate('read_tc_agreement') !!}
                    </label>

                    <input type="text" class="more_info" name="more_info" value="" style="position: absolute; left: -9999px">

                    @if(isset($params['returnTo']))
                        <input type="hidden" name="source" value="{{ $params['source'] }}">
                    @endif

                    @if(isset($params['returnTo']))
                        <input type="hidden" name="returnTo" value="{{ $params['returnTo'] }}">
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-sm-6">
                            <a href="/session" class="btn btn-lg btn-block btn-default" type="submit">{{ translate('have_an_account_button') }}</a>
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-lg btn-block btn-primary">{{ translate('register_button') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

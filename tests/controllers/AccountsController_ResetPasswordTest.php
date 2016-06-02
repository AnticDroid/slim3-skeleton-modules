<?php
namespace Tests\Controllers;

use App\Model\Account;
use App\Model\RecoveryToken;

// for response code list
use MartynBiz\Slim3Controller\Http\Response;

class AccountsController_ResetPasswordTest extends ControllerTestCase
{
    // GET /resetpassword

    // stage 1

    public function test_reset_password_route_without_token_shows_email_form()
    {
        // dispatch
        $this->get('/accounts/resetpassword');

        // assertions
        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryEmailForm
    }

    // stage 2

    public function test_reset_password_post_does_not_send_email_with_invalid_params()
    {
        $this->container['mail_manager']
            ->expects( $this->never() )
            ->method('sendPasswordRecoveryToken');

        // =================================
        // dispatch

        $this->post('/accounts/resetpassword', array(
            'email' => 'invalid@',
        ));

        // =================================
        // assertions

        $this->assertStatusCode( Response::HTTP_BAD_REQUEST );
        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryEmailForm
    }

    public function test_reset_password_post_does_not_send_email_with_honey_pot_filled()
    {
        $this->container['mail_manager']
            ->expects( $this->never() )
            ->method('sendPasswordRecoveryToken');

        // =================================
        // dispatch

        $this->post('/accounts/resetpassword', array(
            'email' => $this->account->email,
            'more_info' => 'i am a bot, who likes the honey pot, i am a bot...',
        ));

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryEmailForm
    }

    public function test_reset_password_post_does_not_send_email_when_account_not_found()
    {
        $this->container['mail_manager']
            ->expects( $this->never() )
            ->method('sendPasswordRecoveryToken');

        // =================================
        // dispatch

        $this->post('/accounts/resetpassword', array(
            'email' => 'valid_email@idontexist.com',
        ));

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryEmailForm
    }

    public function test_reset_password_post_sends_email_with_valid_params()
    {
        $this->container['mail_manager']
            ->expects( $this->once() )
            ->method('sendPasswordRecoveryToken');

        // =================================
        // dispatch

        $this->post('/accounts/resetpassword', array(
            'email' => $this->account->email,
        ));

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryEmailForm
    }

    // stage 3

    /**
     * @expectedException App\Exception\InvalidRecoveryToken
     */
    public function test_reset_password_route_with_invalid_selector_shows_email_form()
    {
        // =================================
        // dispatch

        $this->get('/accounts/resetpassword?token=invalidtokenYFDG89DG9DGU9FDG98');

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryChangePasswordForm
    }

    /**
     * @expectedException App\Exception\InvalidRecoveryToken
     */
    public function test_reset_password_route_with_invalid_token_shows_email_form()
    {
        // =================================
        // dispatch

        $this->get('/accounts/resetpassword?token=1234567890_invalidtokenYFDG89DG9DGU9FDG98');

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryChangePasswordForm
    }

    public function test_reset_password_get_with_valid_selector_and_token_shows_change_password_form()
    {
        // =================================
        // dispatch

        $this->get('/accounts/resetpassword?token=1234567890_qwertyuiop1234567890');

        // =================================
        // assertions

        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryChangePasswordForm
    }

    // stage 4

    public function test_reset_password_post_with_invalid_password_returns_40x()
    {
        // dispatch
        $this->post('/accounts/resetpassword', array(
            'password' => 'toosimple',
            'password_confirmation' => 'toosimple',
            'token' => '1234567890_qwertyuiop1234567890',
        ));

        // assertions
        $this->assertStatusCode( Response::HTTP_BAD_REQUEST );
        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryChangePasswordForm
    }

    public function test_reset_password_post_with_password_not_same_returns_40x()
    {
        // dispatch
        $this->post('/accounts/resetpassword', array(
            'password' => 'P@55word',
            'password_confirmation' => 'D!ff3rent',
            'token' => '1234567890_qwertyuiop1234567890',
        ));

        // assertions
        $this->assertStatusCode( Response::HTTP_BAD_REQUEST );
        $this->assertController('accounts');
        $this->assertAction('resetpassword');

        /// TODO assert correct view shown - css query #passwordRecoveryChangePasswordForm
    }

    /**
     * @expectedException App\Exception\InvalidRecoveryToken
     */
    public function test_reset_password_post_with_invalid_selector_throws_exception()
    {
        $this->post('/accounts/resetpassword', array(
            'password' => 'P@55word',
            'password_confirmation' => 'P@55word',
            'token' => 'invalidtokenYFDG89DG9DGU9FDG98',
        ));
    }

    /**
     * @expectedException App\Exception\InvalidRecoveryToken
     */
    public function test_reset_password_post_with_invalid_token_throws_exception()
    {
        // dispatch
        $this->post('/accounts/resetpassword', array(
            'password' => 'P@55word',
            'password_confirmation' => 'P@55word',
            'token' => '1234567890_invalidtokenYFDG89DG9DGU9FDG98',
        ));

        // assertions
        $this->assertController('accounts');
        $this->assertAction('resetpassword');
    }

    public function test_reset_password_post_with_valid_selector_token_and_password_returns_200()
    {
        // dispatch
        $this->post('/accounts/resetpassword', array(
            'password' => 'P@55word',
            'password_confirmation' => 'P@55word',
            'token' => '1234567890_qwertyuiop1234567890',
        ));

        // assertions
        $this->assertStatusCode( Response::HTTP_OK );
        $this->assertController('accounts');
        $this->assertAction('resetpassword');
    }
}

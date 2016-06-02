<?php
namespace Tests\Library;

use App\Mail\Manager as MailManager;
use Windwalker\Renderer\BladeRenderer;
use Zend\Mail\Message;
use App\Model\Account;

class MailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\Mail\Transport\TransportInterface
     */
    protected $transportMock;

    /**
     * @var MartynBiz\Slim3View\Renderer
     */
    protected $rendererMock;

    /**
     * @var string
     */
    protected $locale = 'ja';

    /**
     * @var string
     */
    protected $defaultLocale = 'en';

    public function setUp()
    {
        // mock the adapter
        $this->transportMock = $this->getMockBuilder('Zend\\Mail\\Transport\\TransportInterface')
            ->disableOriginalConstructor()
            ->getMock();

        // mock the adapter
        $this->rendererMock = $this->getMockBuilder('Windwalker\\Renderer\\BladeRenderer')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_instantiation()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale);

        // ===================
        // assertions

        $this->assertTrue($mail instanceof MailManager);
    }

    public function test_send_receives_message()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale);
        $message = new Message();

        $this->transportMock
            ->expects( $this->once() )
            ->method('send')
            ->with($message);

        // ===================
        // run

        $mail->send($message);
    }

    public function test_send_welcome_email_calls_send()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale);
        $account = new Account(array(
            'name' => 'Martyn Bissett',
            'email' => 'martyn@example.com'
        ));

        $this->transportMock
            ->expects( $this->once() )
            ->method('send');

        $this->rendererMock
            ->method('render')
            ->willReturn('Some rendered text');

        // ===================
        // run

        $mail->sendWelcomeEmail($account);
    }

    // test when text template missing (first choice, and default locale e.g. "en")

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_send_welcome_throws_exception_when_text_template_not_found_and_default_not_set()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale);
        $account = new Account(array(
            'name' => 'Martyn Bissett',
            'email' => 'martyn@example.com'
        ));

        $this->rendererMock
            ->method('render')
            ->will($this->throwException(new \InvalidArgumentException));

        // ===================
        // run

        $mail->sendWelcomeEmail($account);
    }

    public function test_send_welcome_sends_when_default_text_template_found()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale, $this->defaultLocale);
        $account = new Account(array(
            'name' => 'Martyn Bissett',
            'email' => 'martyn@example.com'
        ));

        $this->rendererMock
            ->expects( $this->at(0) ) // text locale
            ->method('render')
            ->will($this->throwException(new \InvalidArgumentException));

        $this->rendererMock
            ->expects( $this->at(1) ) // text default locale
            ->method('render')
            ->willReturn('Content');

        $this->rendererMock
            ->expects( $this->at(2) ) // html locale
            ->method('render')
            ->willReturn('Content');

        $this->transportMock
            ->expects( $this->once() )
            ->method('send');

        // ===================
        // run

        $mail->sendWelcomeEmail($account);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_send_welcome_throws_exception_when_text_template_including_default_not_found()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale);
        $account = new Account(array(
            'name' => 'Martyn Bissett',
            'email' => 'martyn@example.com'
        ));

        $this->rendererMock
            ->expects( $this->once() )
            ->method('render')
            ->will($this->throwException(new \InvalidArgumentException));

        // ===================
        // run

        $mail->sendWelcomeEmail($account);
    }

    // test when HTML template missing (first choice, and default locale e.g. "en")

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_send_welcome_throws_exception_when_html_template_not_found_and_default_not_set()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale);
        $account = new Account(array(
            'name' => 'Martyn Bissett',
            'email' => 'martyn@example.com'
        ));

        $this->rendererMock
            ->expects( $this->at(0) ) // text locale
            ->method('render')
            ->willReturn('Content');

        $this->rendererMock
            ->expects( $this->at(1) ) // html locale
            ->method('render')
            ->will($this->throwException(new \InvalidArgumentException));

        // ===================
        // run

        $mail->sendWelcomeEmail($account);
    }

    // TODO get this working!
    public function test_send_welcome_sends_when_default_html_template_found()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale, $this->defaultLocale);
        $account = new Account(array(
            'name' => 'Martyn Bissett',
            'email' => 'martyn@example.com'
        ));

        $this->rendererMock
            ->expects( $this->at(0) ) // text locale
            ->method('render')
            ->willReturn('Content');

        $this->rendererMock
            ->expects( $this->at(1) ) // html locale
            ->method('render')
            ->will( $this->throwException(new \InvalidArgumentException) );

        $this->rendererMock
            ->expects( $this->at(2) ) // html default locale
            ->method('render')
            ->willReturn('Content');

        $this->transportMock
            ->expects( $this->once() )
            ->method('send');

        // ===================
        // run

        $mail->sendWelcomeEmail($account);
    }

    // TODO get this working!
    /**
     * @expectedException InvalidArgumentException
     */
    public function test_send_welcome_throws_exception_when_html_template_including_default_not_found()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale, $this->defaultLocale);
        $account = new Account(array(
            'name' => 'Martyn Bissett',
            'email' => 'martyn@example.com'
        ));

        $this->rendererMock
            ->expects( $this->at(0) ) // text locale
            ->method('render')
            ->willReturn('Content');

        $this->rendererMock
            ->expects( $this->at(1) ) // html locale
            ->method('render')
            ->will($this->throwException(new \InvalidArgumentException));

        $this->rendererMock
            ->expects( $this->at(2) ) // html default locale
            ->method('render')
            ->will($this->throwException(new \InvalidArgumentException));

        // ===================
        // run

        $mail->sendWelcomeEmail($account);
    }




    public function test_send_password_link_calls_send()
    {
        $mail = new MailManager($this->transportMock, $this->rendererMock, $this->locale);
        $account = new Account(array(
            'name' => 'Martyn Bissett',
            'email' => 'martyn@example.com'
        ));

        $this->transportMock
            ->expects( $this->once() )
            ->method('send');

        $this->rendererMock
            ->method('render')
            ->willReturn('Some rendered text');

        // ===================
        // run

        $mail->sendPasswordRecoveryToken($account, '123456890_qwertyuiop1234567890');
    }
}

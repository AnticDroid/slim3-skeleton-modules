<?php
namespace Auth;

use Zend\Mail\Transport\TransportInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

use Windwalker\Renderer\RendererInterface;
use Illuminate\View\FileViewFinder;
use Auth\Model\User;

/**
 * This is a mail manager for MWAuth, it just removes the need for mail code
 * stuffing up the controllers, and the repetitiveness of building a Message.
 */
class Manager
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var Zend\I18n\Translator\TranslatorInterface
     */
    protected $i18n;

    /**
     * Pass in the transport object
     * @param TransportInterface $transport
     * @param RendererInterface $renderer
     * @param TranslatorInterface $transport
     * @param string $locale
     * @param string $defaultLocale
     */
    public function __construct(TransportInterface $transport, RendererInterface $renderer, TranslatorInterface $translator, $locale, $defaultLocale=null)
    {
        $this->locale = $locale;
        $this->defaultLocale = $defaultLocale;
        $this->renderer = $renderer;
        $this->transport = $transport;
        $this->translator = $translator;
    }

    /**
     * Send a welcome email when users sign up
     */
    public function sendWelcomeEmail(User $user)
    {
        // create the message body from the templates and data
        $textTemplate = 'auth::emails/welcome-%s-text';
        $htmlTemplate = 'auth::emails/welcome-%s-html';
        $body = $this->createMessageBody($textTemplate, $htmlTemplate, array(
            'account' => $user,
        ));

        // create the message
        $message = new Message();

        $message->setBody($body);
        $message->setFrom('noreply@bisetto.net', 'Martyn Bissett'); // JapanTravel <noreply@japantravel.com>
        $message->addTo($user->email, $user->name);
        $message->setSubject('Welcome to Bisetto.net');

        $message->getHeaders()->get('content-type')->setType('multipart/alternative');
        $message->setEncoding("UTF-8");

        // send
        $this->transport->send($message);
    }

    // /**
    //  * Send a welcome email when users sign up
    //  */
    // public function sendPasswordRecoveryToken(User $user, $emailRecoveryToken)
    // {
    //     // create the message body from the templates and data
    //     $textTemplate = 'emails.resetpassword-%s-text';
    //     $htmlTemplate = 'emails.resetpassword-%s-html';
    //     $body = $this->createMessageBody($textTemplate, $htmlTemplate, array(
    //         'account' => $user,
    //         'token' => $emailRecoveryToken,
    //     ));
    //
    //     // create the message
    //     $message = new Message();
    //
    //     $message->setBody($body);
    //     $message->setFrom('support@japantravel.com', 'JapanTravel team');
    //     $message->addTo($user->email, $user->name);
    //     $message->setSubject('Password recovery');
    //
    //     $message->getHeaders()->get('content-type')->setType('multipart/alternative');
    //     $message->setEncoding("UTF-8");
    //
    //     // send
    //     $this->transport->send($message);
    // }

    /**
     * Will create a Zend\Mime\Message body for Message
     * @param string $textTemplate sprintf format string (e.g. )
     */
    protected function createMessageBody($textTemplateFormat, $htmlTemplateFormat, $data)
    {
        // we don't seem to have an exists function with this library, but it will
        // throw an error if the file doesn't exist. therefor, we will catch the
        // error and assume that we wanna use the default one

        try { // current language
            $textTemplate = sprintf($textTemplateFormat, $this->locale);
            $textContent = $this->renderer->render($textTemplate, $data);
        } catch (\InvalidArgumentException $e) { // fallback locale (e.g. "en")

            // if default is not set, throw the exception from the try block
            if (is_null(@$this->defaultLocale)) throw $e;

            // use default locale template. will throw exception if not found
            $textTemplate = sprintf($textTemplateFormat, $this->defaultLocale);
            $textContent = $this->renderer->render($textTemplate, $data);
        }

        $text = new MimePart($textContent);
        $text->type = "text/plain";

        try { // current language
            $htmlTemplate = sprintf($htmlTemplateFormat, $this->locale);
            $htmlContent = $this->renderer->render($htmlTemplate, $data);
        } catch (\InvalidArgumentException $e) { // fallback locale (e.g. "en")

            // if default is not set, throw the exception from the try block
            if (is_null(@$this->defaultLocale)) throw $e;

            // use default locale template. will throw exception if not found
            $htmlTemplate = sprintf($htmlTemplateFormat, $this->defaultLocale);
            $htmlContent = $this->renderer->render($htmlTemplate, $data);
        }

        $html = new MimePart($htmlContent);
        $html->type = "text/html";

        // build the body from text and html parts
        $body = new MimeMessage();
        $body->setParts(array($text, $html));

        return $body;
    }
}

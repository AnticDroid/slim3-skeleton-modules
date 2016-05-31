<?php
namespace Application;

use Zend\Mail\Transport\TransportInterface;
use League\Plates\Engine;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Auth\Model\User;

/**
 * This is a mail manager for MWAuth, it just removes the need for mail code
 * stuffing up the controllers, and the repetitiveness of building a Message.
 */
class Mail
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
     * @var Engine
     */
    protected $renderer;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Pass in the transport object
     * @param Zend\Mail\Transport\TransportInterface $transport
     * @param MartynBiz\Slim3View\Renderer $language
     * @param string $language
     */
    public function __construct(TransportInterface $transport, Engine $renderer, TranslatorInterface $translator, $locale, $defaultLocale=null)
    {
        $this->transport = $transport;
        $this->renderer = $renderer;
        $this->translator = $translator;
        $this->locale = $locale;
        $this->defaultLocale = $defaultLocale;
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
            'user' => $user,
        ));

        // create the message
        $message = new Message();

        $message->setBody($body);
        $message->setFrom('noreply@japantravel.com', 'Bisetto');
        $message->addTo($user->email, $user->name);
        $message->setSubject('Welcome to Bisetto');

        $message->getHeaders()->get('content-type')->setType('multipart/alternative');
        $message->setEncoding("UTF-8");

        // send
        $this->transport->send($message);
    }

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

<?php

namespace App\Services;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    // Test injection de paramètres de configuration
    private $replyTo;

    public function __construct(private MailerInterface $mailer, $replyTo){
        $this->replyTo = $replyTo;
    }

    public function sendEmail(
        $to = 'john.doe.sloubi1962@gmail.com',
        $content = '<p>See Twig integration for better HTML integration!</p>',
        $subject = 'Time for Symfony Mailer!'
    ): void
    {
        //dd($this->replyTo);
        $email = (new Email())
            ->from('hello@example.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo($this->replyTo)
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text('Sending emails is fun again!')
            ->html($content);

        $this->mailer->send($email);
    }
}
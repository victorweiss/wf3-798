<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(array $data): void
    {
        if (!isset($data['from'])) {
            $data['from'] = 'monemail@email.com';
        }

        if (!isset($data['to'])) {
            $data['to'] = 'monemail@email.com';
        }

        $email = (new Email())
            ->from($data['from'])
            ->to($data['to'])
            ->replyTo($data['replyTo'])
            ->subject('sujet')
            ->text($data['message']);



        dd($email);
    }
}

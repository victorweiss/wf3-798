<?php

namespace App\Service;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;
    private $emailAdmin;
    private $appEnv;
    private $logger;

    public function __construct(
        string $emailAdmin,
        string $appEnv,
        MailerInterface $mailer,
        LoggerInterface $logger
    ) {
        $this->emailAdmin = $emailAdmin;
        $this->mailer = $mailer;
        $this->appEnv = $appEnv;
        $this->logger = $logger;
    }

    public function send(array $data): bool
    {
        if ($this->appEnv === 'dev') {
            if (!isset($data['subject'])) {
                throw new Exception("You should specify a subject");
            }
            $data['to'] = $this->emailAdmin;
        }

        $email = (new Email())
            ->from($data['from'] ?? $this->emailAdmin)
            ->to($data['to'] ?? $this->emailAdmin)
            ->replyTo($data['replyTo'] ?? $data['from'] ?? $this->emailAdmin)
            ->subject($data['subject'] ?? 'Mon site')
            ->text($data['message']);

        try {
            $this->mailer->send($email);
            return true;
        } catch (Exception $e) {
            $this->logger->alert(sprintf("%s in %s at %s : %s", __FUNCTION__, __FILE__, __LINE__, $e->getMessage()));
        }

        return false;
    }
}

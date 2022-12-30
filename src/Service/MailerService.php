<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
// use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class MailerService
{
    public function __construct(private TransportInterface $mailer) 
    {}
    public function sendEmail(
        $to = 'rodolphe.mingo@yahoo.com',
        $content = '<p>See Twig integration for better HTML integration!</p>',
        $subject = 'Test Final !'
    ): void

    {

        $email = (new Email())
            ->from('fayaflame@gmail.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text('Sending emails is fun again!')
            ->html($content);


            // $stream = new SocketStream(); 

            // $stream->disableTls();
            // dd($stream->isTLS());
            
                $this->mailer->send($email);
             
         

        // ...
    }
}

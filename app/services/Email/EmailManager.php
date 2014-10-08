<?php namespace Kevin\Email;

use \SendGrid;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_SmtpTransport;

class EmailManager {

    public function parse($from, $to, $cc, $subject, $message) {
        return new ParsedEmail($this, $from, $to, $cc, $subject, $message);
    }

    public function send(ResponseEmail $response) {
        $sendgrid_username = $_ENV['SENDGRID_USERNAME'];
        $sendgrid_password = $_ENV['SENDGRID_PASSWORD'];

        $to                = $response->getTo();
        $cc                = $response->getCC();
        $from              = $response->getFrom();
        $subject           = $response->getSubject();
        $html              = $response->getMessage();

        $transport  = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 587);
        $transport->setUsername($sendgrid_username);
        $transport->setPassword($sendgrid_password);


        $mailer     = Swift_Mailer::newInstance($transport);

        $message    = new Swift_Message();
        $message->setTo($to);
        $message->setCc($cc);
        $message->setFrom($from);
        $message->setSubject($subject);
        $message->setBody($html, 'text/html');
        $message->addPart(strip_tags($html), 'text/plain');

        try {
          $status = $mailer->send($message);
        } catch(\Swift_TransportException $e) {
          \Log::error($e->getMessage());
        }

        \Log::debug('results', compact('from', 'to', 'cc', 'status'));
        return $response;
    }
}

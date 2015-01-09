<?php namespace Kevin\Email;

use \SendGrid;
use \Swift_Mailer;
use \Swift_Message;
use \Swift_SmtpTransport;

class EmailManager {

    public function parse($from, $to, $cc, $subject, $message, $headers = array()) {
        return new ParsedEmail($this, $from, $to, $cc, $subject, $message, $headers);
    }

    public function send(ResponseEmail $response) {
        $port = 587;

        switch($_ENV['SEND_ENGINE']) {
        case 'sendgrid':
            $username = $_ENV['SENDGRID_USERNAME'];
            $password = $_ENV['SENDGRID_PASSWORD'];
            $server = 'smtp.sendgrid.net';
            break;
        case 'mandrill':
            $username = $_ENV['MANDRILL_USERNAME'];
            $password = $_ENV['MANDRILL_APIKEY'];
            $server = 'smtp.mandrillapp.com';
            break;
        default:
            throw new Exception("SEND_ENGINE not configured correctly, ".$_ENV['SEND_ENGINE']." unknown.");
        }

        $to                = $response->getTo();
        $cc                = $response->getCC();
        $from              = $response->getFrom();
        $subject           = $response->getSubject();
        $html              = $response->getMessage();

        $transport  = Swift_SmtpTransport::newInstance($server, $port);
        $transport->setUsername($username);
        $transport->setPassword($password);

        
        $mailer     = Swift_Mailer::newInstance($transport);

        $message    = new Swift_Message();
        $message->setTo($to);
        $message->setCc($cc);
        $message->setFrom($from);
        $message->setSubject($subject);
        $message->setBody(strip_tags($html));
        $message->addPart($html, 'text/html');

        foreach($response->getHeaders() as $key => $value) {
            $message->getHeaders()->addTextHeader($key, $value);
        }

        try {
          $status = $mailer->send($message);
        } catch(\Swift_TransportException $e) {
          \Log::error($e->getMessage());
        }

        \Log::debug('results', compact('from', 'to', 'cc', 'status'));
        return $response;
    }
}

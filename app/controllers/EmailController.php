<?php

class EmailController extends BaseController {

    public $email = null;

    public function EmailController() {
        $this->email = App::make('email');
    }

    public function postHookContextio() {
        Log::debug(json_encode(Input::all()));
        $messageData = Input::get('message_data');
        $messageId   = data_get($messageData, "message_id");
        $accountId   = $_ENV['CONTEXTIO_ACCOUNT_ID'];
        $key         = $_ENV['CONTEXTIO_KEY'];
        $secret      = $_ENV['CONTEXTIO_SECRET'];
        $contextIO   = new ContextIO($key, $secret);
        
        $message = $contextIO
            ->getMessageBody($accountId, $messageId)
            ->getData()[0]['content']
        ;

        $from    = data_get($messageData, "addresses.from.email"); 
        $to      = data_get($messageData, "addresses.to");
        $cc      = data_get($messageData, "addresses.cc") ?: []; 
        $subject = data_get($messageData, "subject"); 

        $to = array_pluck($to, 'email');
        $cc = array_pluck($cc, 'email');

        $email = $this->email
            ->parse($from, $to, $cc, $subject, $message)
            ->process()
            ->send()
        ;

        Log::debug($email->getMessage());
        return View::make('hook', ['email' => $email]);
    }

    public function postHookSendgrid() {
        $from     = Input::get('from');
        $to       = explode(",", Input::get("to"));
        $cc       = explode(",", Input::get("cc") ?: "");
        $subject  = Input::get('subject');
        $message  = Input::get('text') ?: Input::get('html');
        $message  = preg_replace('/(^\w.+:\n)?(^>.*(\n|$))+/mi', '', $message);
        $headers = [];
        foreach(preg_split("/[\r\n]+/", Input::get('headers')) as $header) {
            $idx = strpos($header, ':');
            $headers[substr($header, 0, $idx)] = substr($header, $idx+1);
        }

        Log::debug("details", compact('from', 'to', 'cc', 'subject', 'message', 'headers'));

        $email = $this->email
            ->parse($from, $to, $cc, $subject, $message, $headers)
            ->process()
            ->send()
        ;

        Log::debug("response", [$email->getMessage()]);

        return Response::make('ok', 200);
    }

    public function postHookMandrill() {
        $msg = (object)json_decode(Input::get('mandrill_events'))[0]->msg;

        

        $from     = $msg->from_email;
        $to       = array_map(function($i) { return $i[0]; }, $msg->to);
        $cc       = array_map(function($i) { return $i[0]; }, isset($msg->cc) ? $msg->cc : []);
        $subject  = $msg->subject;
        $message  = $msg->text ?: $msg->html;
        $message  = preg_replace('/(^\w.+:\n)?(^>.*(\n|$))+/mi', '', $message);
        $headers = [];
        foreach(preg_split("/[\r\n]+/", Input::get('headers')) as $header) {
            $idx = strpos($header, ':');
            $headers[substr($header, 0, $idx)] = substr($header, $idx+1);
        }

        Log::debug("details", compact('from', 'to', 'cc', 'subject', 'message', 'headers'));

        $email = $this->email
            ->parse($from, $to, $cc, $subject, $message, $headers)
            ->process()
            ->send()
        ;

        Log::debug("response", [$email->getMessage()]);

        return Response::make('ok', 200);
    }
}

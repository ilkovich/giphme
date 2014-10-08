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
        Log::debug("details", compact('from', 'to', 'cc', 'subject', 'message'));

        $email = $this->email
            ->parse($from, $to, $cc, $subject, $message)
            ->process()
            ->send()
        ;

        Log::debug("response", [$email->getMessage()]);

        return Response::make('ok', 200);
    }
}
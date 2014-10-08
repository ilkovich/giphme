<? namespace Kevin\Email;

class ParsedEmail {
    protected $message;
    protected $from;
    protected $subject;
    protected $to;
    protected $cc;

    protected $hashtags;

    protected $handlerChain;
    
    protected $manager;

    public function __construct($manager, $from, $to, $cc, $subject, $message) {
        $this->message    = $message;
        $this->cc         = $cc;
        $this->to         = $to;
        $this->from       = $from;
        $this->subject    = $subject;
        $this->manager    = $manager;
        $this->handlerChain = [\App::make('giphy')]; 

        $this->parse();
    }

    public function parse() {
        preg_match_all('@(\W|^)#([a-zA-Z\-_0-9]+)@', strip_tags($this->message), $matches);
        array_shift($matches);
        $this->hashtags = $matches[1];
    }

    public function process() {
        $response = $this->generateResponse();

        foreach($this->getHandlerChain() as $handler) {
            $handler->run($this, $response);
        }

        return $response;
    }

    public function generateResponse() {

        //prevent loops
        if(in_array($this->from, array_keys(\Config::get('app.emails.fromAddress')))) {
            \Log::error("Invalid from address is {$this->from}");
            die;
        }

        $to = $this->sanitizeAddresses( [$this->from] );
        
        $cc = array_merge($this->cc, $this->to);

        $ignoredPatterns = \Config::get('app.emails.ignoredPatterns');

        \Log::debug('ccs', compact('cc'));

        $cc = array_filter($cc, function($i) use ($ignoredPatterns) {
            if(empty($i)) return false;

            foreach($ignoredPatterns as $pattern) {
                if(preg_match($pattern, $i)) return false;
            }

            return true;
        });

        \Log::debug('filtered ccs', compact('cc'));

        $cc = $this->sanitizeAddresses($cc);

        \Log::debug('sanitized ccs', compact('cc'));

        return new ResponseEmail( 
            $this->manager, 
            \Config::get('app.emails.fromAddress'),
            $to, 
            $cc, 
            $this->subject
        );
    }

    public function sanitizeAddresses($addresses) {
        $arr = [];
        foreach($addresses as $address) {
            if(preg_match('@(.*)<(.*?)>@', $address, $matches)) {
                $name          = trim($matches[1], " \t\n\r\0\x0B[]");
                $addr          = $matches[2];
                $arr[$addr]    = $name;
            } else {
                $arr[$address] = null;
            }
        }

        return $arr;
    }

    //ACCESSORS 
    public function getHandlerChain() {
        return $this->handlerChain;
    }

    public function getHashtags() {
        return $this->hashtags;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getCC() {
        return $this->cc;
    }

    public function getTo() {
        return $this->to;
    }
    //END ACCESSORS
}


<? namespace Kevin\Email;

class ResponseEmail {
    protected $message;
    protected $cc;
    protected $to;
    protected $from;
    protected $subject;
    protected $manager;

    protected $headers = [];

    public function __construct($manager, $from, $to, $cc, $subject, $message="") {
        $this->message    = $message;
        $this->cc         = $cc;
        $this->to         = $to;
        $this->manager    = $manager;
        $this->from       = $from;
        $this->subject    = "Re: $subject";
    }

    public function send($sendEmpty = false) {
        if($sendEmpty || !$this->isEmpty()) 
            return $this->manager->send($this);

        return $this;
    }

    public function prepend($str) {
        $this->message = $str . $this->message;
    }

    public function append($str) {
        $this->message .= $str;
    }

    public function addHeader($key, $value) {
        \Log::debug("Adding header $key with '$value'");
        $this->headers[$key] = $value;

        return $this;
    }


    //ACCESSORS

    public function getHeaders() {
        return $this->headers;
    }

    public function getMessage() {
        return "<html><head></head><body>".$this->message."</body></html>";
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function getCC() {
        return $this->cc;
    }

    public function setCC($cc) {
        $this->cc = $cc;
    }

    public function getTo() {
        return $this->to;
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function getFrom() {
        return $this->from;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function isEmpty() {
        return empty($this->message);
    }
}

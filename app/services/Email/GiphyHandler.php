<? namespace Kevin\Email;

use \rfreebern\Giphy;

class GiphyHandler {
    public function run(ParsedEmail $email, ResponseEmail $response) {
        $message = $response->getMessage();
        $tpl = '<img src="%s" alt="%s" />';
        $images = [];
        
        foreach($email->getHashtags() as $hashtag) {
            //TODO error handling
            \Log::info($hashtag);

            $res = (new Giphy)->search($hashtag);
            $idx = rand(0,count($res->data)-1);
            if($url = data_get($res, "data.$idx.images.original.url")) {
                $images[] = sprintf($tpl, $url, $hashtag);
            }  
        }

        if(!empty($images))
            $response->prepend("<div>".implode($images, '<br/>')."</div>");
        else 
            \Log::error("no images");
    }
}

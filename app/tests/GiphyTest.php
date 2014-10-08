<?php

use \Kevin\Email\ParsedEmail;
use \Kevin\Email\ResponseEmail;

class GiphyTest extends TestCase {

    public function getMessage() {
        $email = App::make('email');

        $message=<<<EOF
#new-years

#dog

#tiger
EOF;
        return $email->parse(
            "test@test.com",  //FROM
            ["test@test.com"],  //TO
            ["test@test.com"],  //CC
            "SUBJECT",        //SUBJECT
            $message          //MSG
        );
    }

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testParse() {
        $hashtags = $this->getMessage()->getHashtags();
        $this->assertEquals(count($hashtags), 3);
        $this->assertEquals($hashtags[0], "new-years");
        $this->assertEquals($hashtags[1], "dog");
        $this->assertEquals($hashtags[2], "tiger");
	}

    public function testGiphy() {
        $giphy = App::make('giphy');
        $email = App::make('email');

        $parsedEmail = $this->getMessage();
        $response    = $parsedEmail->generateResponse();

        $giphy->run($parsedEmail, $response);

        $this->assertNotEmpty($response->getMessage());

        var_dump($response->getMessage());
    }

}

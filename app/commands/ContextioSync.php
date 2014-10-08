<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ContextioSync extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'contextio:sync';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire() {
        $accountId = $_ENV['CONTEXTIO_ACCOUNT_ID'];
        $key       = $_ENV['CONTEXTIO_KEY'];
        $secret    = $_ENV['CONTEXTIO_SECRET'];
		$contextIO = new ContextIO($key, $secret);

        $result = $contextIO->syncSource($accountId);
        $this->info(json_encode($result->getData()));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}

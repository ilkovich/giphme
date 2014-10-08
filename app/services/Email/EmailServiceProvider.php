<? namespace Kevin\Email;

use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->singleton('email', function() {
            return new EmailManager;
        });

        $this->app->singleton('giphy', function() {
            return new GiphyHandler;
        });
    }
}

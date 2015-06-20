<?php namespace TheSnackalicious\CombineWords\Providers;

use Illuminate\Support\ServiceProvider;
use TheSnackalicious\CombineWords\Generators\Generator;
use TheSnackalicious\CombineWords\Generators\GeneratorContract;

class CombineWordsServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $configPath;

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->configPath = __DIR__ . '/../config/combinewords.php';
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([ $this->configPath => config_path('combinewords.php') ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath, 'combinewords');

        $this->app->singleton(GeneratorContract::class, function() {
            return new Generator(
                config('combinewords.directory'),
                config('combinewords.max_attempts', null)
            );
        });
    }
}
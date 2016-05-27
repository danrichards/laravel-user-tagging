<?php

namespace Dan\Tagging\Testing\Integration;

use Faker\Generator;
use IntegrationTestsSeeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\ClassFinder;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Testing\TestCase;
use Dan\Tagging\Testing\Integration\Setup\User;
use Dan\Tagging\Providers\TaggingServiceProvider;
use Dan\Tagging\Testing\Integration\Setup\Users\UsersInterface;
use Dan\Tagging\Testing\Integration\Setup\Posts\PostsInterface;
use Torann\LaravelRepository\Providers\RepositoryServiceProvider;

/**
 * Class IntegrationTestCase
 *
 * Properties consist of the models made by our IntegrationTestsSeeder.
 */
abstract class IntegrationTestCase extends TestCase
{

    /** @var bool $cache */
    public static $cache = false;

    /** @var \IntegrationTestsSeeder $seeded */
    public $seeded;

    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../vendor/laravel/laravel/bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    /**
     * Setup DB before each test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default','sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        if (self::$cache) {
            $this->app['config']->set('repositories.cache.enabled', true);
            $this->app['config']->set('cache.default', 'memcached');
        } else {
            $this->app['config']->set('repositories.cache.enabled', false);
        }

        // Taggable Interfaces for Abstract Taggable Repositories
        $this->app['config']->set('tagging.taggable_interfaces', [
            '\Dan\Tagging\Testing\Integration\Setup\Post' => PostsInterface::class
        ]);

        // Repository Interface for Users Repository
        $this->app['config']->set('tagging.users_interface', UsersInterface::class);

        // Do not override unless you also override the Repository
        $this->app['config']->set('tagging.user_model', User::class);

        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(TaggingServiceProvider::class);

        $this->app->singleton(Factory::class, function ($app){
            return Factory::construct($app->make(Generator::class), __DIR__.'/../../database/factories');
        });

        $this->migrate();

        // Provides 3 users, 5 posts, and a bunch of tagging.
        $this->seeded = $this->seed();
    }

    /**
     * Run package database migrations
     *
     * @return void
     */
    public function migrate($up = true)
    {
        $fileSystem = new Filesystem;
        $classFinder = new ClassFinder;

        foreach($fileSystem->files(__DIR__ . "/../../database/migrations") as $file)
        {
            $fileSystem->requireOnce($file);
            $migrationClass = $classFinder->findClass($file);

            if ($up) {
                (new $migrationClass)->up();
            } else {
                (new $migrationClass)->down();
            }
        }
    }

    /**
     * Run package database migrations
     *
     * @param string $class Merely here to appease PHP Strict standards.
     * @return \IntegrationTestsSeeder
     */
    public function seed($class = 'ignored')
    {
        $fileSystem = new Filesystem;
        $classFinder = new ClassFinder;

        $seeder = __DIR__.'/../../database/seeds/IntegrationTestsSeeder.php';
        $fileSystem->requireOnce($seeder);
        $seederClass = $classFinder->findClass($seeder);

        return (new $seederClass)->run();
    }

    public function tearDown()
    {
        $this->migrate(false);
        parent::tearDown();  // Moving that call to the top of the function didn't work either.
    }

}
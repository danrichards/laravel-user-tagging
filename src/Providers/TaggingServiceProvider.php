<?php 

namespace Dan\Tagging\Providers;

use Dan\Tagging\Util;
use Dan\Tagging\RepositoryFactory;
use Illuminate\Support\ServiceProvider;
use Dan\Tagging\Contracts\TaggingUtility;

/**
 * Copyright (C) 2014 Robert Conner
 */
class TaggingServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../config/tagging.php' => config_path('tagging.php'),
			__DIR__.'/../../config/repositories.php' => config_path('repositories.php'),
			__DIR__.'/../../database/migrations/2014_01_07_073615_create_tagged_table.php' => $this->app->databasePath().('/migrations/2014_01_07_073615_create_tagged_table.php'),
			__DIR__.'/../../database/migrations/2014_01_07_073615_create_tags_table.php' => $this->app->databasePath().('/migrations/2014_01_07_073615_create_tags_table.php'),
			__DIR__.'/../../database/migrations/2014_01_07_073615_create_tagged_user_table.php' => $this->app->databasePath().('/migrations/2014_01_07_073615_create_tagged_user_table.php'),
		]);
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() 
	{
		$utility = $this->app['config']['tagging.utility']
			?: '\Dan\Tagging\Util';
		
		$this->app->singleton(TaggingUtility::class, $utility);

		$createFunc = $this->app['config']['repositories.cache']
			? "createWithCache" : "create";

		$interface = $this->app['config']['tagging.users_interface'] 
			?: '\Dan\Tagging\Repositories\Users\UsersInterface';
		
		$this->app->bind($interface, 
			function ($app) use ($createFunc, $interface) {
				return call_user_func_array(
					[RepositoryFactory::class, $createFunc], 
					['Users', $this->getNamespace($interface, 2)]
				);
			}
		);

		$interface = $this->app['config']['tagging.tags_interface'] 
			?: '\Dan\Tagging\Repositories\Tags\TagsInterface';
		
		$this->app->bind($interface, 
			function ($app) use ($createFunc, $interface) {
				return call_user_func_array(
					[RepositoryFactory::class, $createFunc], 
					['Tags', $this->getNamespace($interface, 2)]
				);
			}
		);

		$interface = $this->app['config']['tagging.tagged_interface'] 
			?: '\Dan\Tagging\Repositories\Tagged\TaggedInterface';
		
		$this->app->bind($interface, 
			function ($app) use ($createFunc, $interface) {
				return call_user_func_array(
					[RepositoryFactory::class, $createFunc], 
					['Tagged', $this->getNamespace($interface, 2)]
				);
			}
		);

		$interface = $this->app['config']['tagging.tagged_user_interface'] 
			?: '\Dan\Tagging\Repositories\TaggedUser\TaggedUserInterface';
		
		$this->app->bind($interface, 
			function ($app) use ($createFunc, $interface) {
				return call_user_func_array(
					[RepositoryFactory::class, $createFunc], 
					['TaggedUser', $this->getNamespace($interface, 2)]
				);
			}
		);

		$taggables = $this->app['config']['tagging.taggable_interfaces'] ?: [];
		
		foreach ($taggables as $model => $interface) {
			$name = explode('\\', $model);
			$taggable = str_plural(end($name));
			$this->app->bind($interface, 
				function ($app) use ($createFunc, $taggable, $interface) {
					return call_user_func_array(
						[RepositoryFactory::class, $createFunc], 
						[$taggable, $this->getNamespace($interface, 2)]
					);
				}
			);
		}		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Illuminate\Support\ServiceProvider::provides()
	 */
	public function provides()
	{
		return [TaggingUtility::class];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Illuminate\Support\ServiceProvider::when()
	 */
	public function when()
	{
		return array('artisan.start');
	}

	/**
	 * @param string $class
	 * @param int $pop
	 * @return mixed
	 */
	private function getNamespace($class, $pop = 1)
	{
		$namespace = explode('\\', $class);
		while($pop--) {
			array_pop($namespace);
		}
		return implode('\\', $namespace);
	}
	
}

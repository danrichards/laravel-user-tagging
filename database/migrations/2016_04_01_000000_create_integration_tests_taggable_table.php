<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateIntegrationTestsTable
 *
 * This migration is purely here for testing purposes. The service provider
 * will NOT migrate it into your project. :)
 */
class CreateIntegrationTestsTaggableTable extends Migration {

	public function up()
	{
		Schema::create('posts', function(Blueprint $table) {
			$table->increments('id');
			$table->string('title', 255);
			$table->text('body');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('posts');
	}
}

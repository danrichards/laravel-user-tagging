<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateUserTaggedTable
 *
 * Migration to create a pivot table for connecting users that have tagged
 * specific models with specific tags.
 */
class CreateTaggedUserTable extends Migration {

	public function up() {
		Schema::create('tagging_tagged_user', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('tagged_id')->unsigned()->index();
			$table->integer('user_id');
			$table->timestamps();
			$table->index(['tagged_id'], 'tagging_tagged_id');
			$table->index(['user_id'], 'tagging_user_id');
			$table->index(['tagged_id', 'user_id'], 'tagging_tagged_user_ids');
		});
	}

	public function down() {
		Schema::drop('tagging_tagged_user');
	}
}

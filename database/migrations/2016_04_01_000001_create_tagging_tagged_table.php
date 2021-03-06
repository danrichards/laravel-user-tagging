<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaggedTable extends Migration {

	public function up() {
		Schema::create('tagging_tagged', function(Blueprint $table) {
			$table->increments('id');
			if(\Config::get('tagging.primary_keys_type') == 'string') {
				$table->string('taggable_id', 36)->index();
			} else {
				$table->integer('taggable_id')->unsigned()->index();
			}
			$table->string('taggable_type', 255)->index();
			$table->string('tag_name', 255);
			$table->string('tag_slug', 255)->index();
			$table->integer('users_count')->unsigned()->default(1);
			$table->index(['taggable_type', 'taggable_id', 'tag_slug'], 'tagged_tagged_model_id_tag_unique_index');
		});
	}

	public function down() {
		Schema::drop('tagging_tagged');
	}
}

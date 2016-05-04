<?php

return [

	// For migrations, may be "integer" or "string"
	'primary_keys_type' => 'integer',

	// How we will slug our tags before persisting them.
	'normalizer' => '\Dan\Tagging\Util::slug',

	// How we will display (title) our tags before persisting them.
	'displayer' => '\Illuminate\Support\Str::title',

	// Taggable Interfaces for Abstract Taggable Repositories
//	'taggable_interfaces' => [
//		'\App\Models\SomeModel' => '\App\Repositories\SomeModels\SomeModelsInterface'
//	],

	// Repository Interface for Users Repository
//	'users_interface' => '\Dan\Tagging\Repositories\Users\UsersInterface',

	// Do not override unless you also override the Repository
//	'user_model' => '\App\User',

	// Repository Interface for Tags Repository
//	'tags_interface' => '\Dan\Tagging\Repositories\Users\TagsInterface',

	// Do not override unless you also override the Repository
//	'tag_model' => '\Dan\Tagging\Models\Tag',

	// Repository Interface for Tagged Repository
//	'tagged_interface' => '\Dan\Tagging\Repositories\Tagged\TaggedInterface',

	// Do not override unless you also override the Repository
//	'tagged_model' => '\Dan\Tagging\Models\Tagged',

	// Repository Interface for Tagged Repository
//	'tagged_user_interface' => '\Dan\Tagging\Repositories\TaggedUser\TaggedUserInterface',

	// Do not override unless you also override the Repository
//	'tagged_user_model' => '\Dan\Tagging\Models\TaggedUser'

];
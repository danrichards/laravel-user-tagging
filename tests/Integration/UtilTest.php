<?php

namespace Dan\Tagging\Testing\Integration;

use Dan\Tagging\Models\Tagged;
use Dan\Tagging\Util;

/**
 * Class UtilTest
 */
class UtilTest extends IntegrationTestCase {

	public function data_provider_test_it_makes_a_slug_array()
	{
		$tags = collect([
			$this->seeded->tags['laravel'],
			$this->seeded->tags['lumen'],
			$this->seeded->tags['spark'],
		]);
		$tagged = collect([
			new Tagged(['tag_slug' => 'laravel']),
			new Tagged(['tag_slug' => 'lumen']),
			new Tagged(['tag_slug' => 'spark'])
		]);
		return [
			['laravel,lumen,spark'],
			[['laravel', 'lumen', 'spark']],
			['Laravel, Lumen, Spark'],
			[['Laravel', 'Lumen', 'Spark']],
			[$tags],
			[$tagged]
		];
	}

	/**
	 * @dataProvider data_provider_test_it_makes_a_slug_array
	 * @param $input
	 */
	public function test_it_makes_a_slug_array($input)
	{
		$this->assertEquals(
			['laravel', 'lumen', 'spark'],
			Util::makeSlugArray($input)
		);
	}
	
}
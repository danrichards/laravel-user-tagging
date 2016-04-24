<?php

namespace Dan\Tagging\Testing\Integration\Repositories;

use Dan\Tagging\Models\Tag;
use Dan\Tagging\Models\Tagged;
use Dan\Tagging\Repositories\Tags\TagsInterface;
use Dan\Tagging\Testing\Integration\IntegrationTestCase;
use Illuminate\Support\Collection;

/**
 * Class TagsRepositoryTests
 *
 * @see \IntegrationTestsSeeder
 */
class TagsRepositoryTests extends IntegrationTestCase
{
    
    /** @var \Dan\Tagging\Repositories\Tags\TagsRepository $repo */
    protected $repo;
    
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->repo = app(TagsInterface::class);
    }

    public function data_provider_test_it_gets_users_who_have_used_a_specific_tag()
    {
        $tag = new Tag();
        $tag->slug = 'laravel';
        return [
            ['laravel'],
            [$tag]
        ];
    }

    /**
     * @dataProvider data_provider_test_it_gets_users_who_have_used_a_specific_tag
     * @param $tag
     */
    public function test_it_gets_users_who_have_used_a_specific_tag($tag) {
        $this->assertEquals(
            ['Alice', 'Bob', 'Charlie'],
            $this->repo->usersFor($tag)->pluck('name')->all()
        );
    }

    public function test_it_finds_a_tag()
    {
        $tag = $this->repo->findTag('laravel');
        $this->assertEquals([
            'id' => 1,
            'slug' => 'laravel',
            'name' => 'Laravel',
            'count' => 1
        ], $tag->toArray());
    }

    public function data_provider_test_it_finds_tags()
    {
        return [
            ['frameworks,laravel,lumen'],
            [['frameworks', 'laravel', 'lumen']],
            [collect([new Tagged(['tag_slug' => 'laravel']), new Tagged(['tag_slug' => 'lumen']), new Tagged(['tag_slug' => 'frameworks'])])]
        ];
    }

    /**
     * @dataProvider data_provider_test_it_finds_tags
     * @param $tags
     */
    public function test_it_finds_tags($tags)
    {
        $tags = $this->repo->findTags($tags);
        $this->assertInstanceOf(Collection::class, $tags);
        $this->assertEquals(
            ['frameworks', 'laravel', 'lumen'],
            $tags->pluck('slug')->all()
        );
    }

    public function test_it_finds_or_creates_a_tag()
    {
        $tag = $this->repo->findTag('taco');
        $this->assertNull($tag);

        $tag = $this->repo->findOrCreate('Taco', $tagWasCreated);
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals('taco', $tag->slug);
    }

    public function test_it_recalculates()
    {
        // Verify count is 1
        $tag = Tag::where('slug', 'laravel')->first();
        $this->assertEquals(1, $tag->count);

        // Tag something
        factory(\Dan\Tagging\Models\Tagged::class)->create([
            'taggable_id' => $this->seeded->posts['lumen']->getKey(),
            'taggable_type' => get_class($this->seeded->posts['spark']),
            'tag_name' => $this->seeded->tags['laravel']->name,
            'tag_slug' => $this->seeded->tags['laravel']->slug
        ]);

        // Verify count is 2 after recalculating
        $this->repo->recalculate($tag);
        $tag = Tag::where('slug', 'laravel')->first();
        $this->assertEquals(2, $tag->count);
    }

}
<?php

namespace Dan\Tagging\Testing\Integration\Repositories;

use Dan\Tagging\Models\Tag;
use Dan\Tagging\Models\Tagged;
use Dan\Tagging\Models\TaggedUser;
use Dan\Tagging\Repositories\Tagged\TaggedInterface;
use Dan\Tagging\Testing\Integration\IntegrationTestCase;
use Dan\Tagging\Testing\Integration\Setup\Post;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TaggedRepositoryTests
 *
 * @see \IntegrationTestsSeeder
 */
class TaggedRepositoryTests extends IntegrationTestCase
{
    /** @var \Dan\Tagging\Repositories\Tagged\TaggedRepository $repo */
    protected $repo;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->repo = app(TaggedInterface::class);
    }

    public function test_it_finds_by_model_key_slug()
    {
        $repo = $this->repo;
        $class = get_class($this->seeded->posts['spark']);
        $id = $this->seeded->posts['spark']->getKey();
        
        $eloquent = Tagged::where('taggable_type', $class)
            ->where('taggable_id', $id)
            ->first();
        
        $tagged = $repo->findByModelKeySlug($class, $id, $eloquent->tag_slug);
        
        $this->assertInstanceOf(Tagged::class, $tagged);
        $this->assertEquals($eloquent->toArray(), $tagged->toArray());
    }

    public function test_it_finds_by_taggable_tag()
    {
        $repo = $this->repo;
        $taggable = $this->seeded->posts['spark'];
        $tag = $this->seeded->tags['laravel'];
        
        /** @var \Dan\Tagging\Models\Tagged $eloquent */
        $eloquent = Tagged::where('taggable_type', get_class($taggable))
            ->where('taggable_id', $taggable->getKey())
            ->where('tag_slug', $tag->slug)
            ->first();

        $tagged = $repo->findByTaggableTag($taggable, $tag);

        $this->assertInstanceOf(Tagged::class, $tagged);
        $this->assertEquals($eloquent->toArray(), $tagged->toArray());
    }

    public function test_it_finds_by_taggable_tag_or_creates()
    {
        $repo = $this->repo;
        $taggable = $this->seeded->posts['spark'];
        $tag = $this->seeded->tags['metaphors'];

        /** @var \Dan\Tagging\Models\Tagged $eloquent */
        $eloquentBefore = Tagged::where('taggable_type', get_class($taggable))
            ->where('taggable_id', $taggable->getKey())
            ->where('tag_slug', $tag->slug)
            ->first();

        $this->assertNull($eloquentBefore);

        $tagged = $repo->findByTaggableTagOrCreate($taggable, $tag);

        /** @var \Dan\Tagging\Models\Tagged $eloquent */
        $eloquent = Tagged::where('taggable_type', get_class($taggable))
            ->where('taggable_id', $taggable->getKey())
            ->where('tag_slug', $tag->slug)
            ->first();

        $this->assertInstanceOf(Tagged::class, $tagged);

        $expected = $eloquent->toArray();
        unset($expected['users_count']);    // hack, users_count not accurate until recalculation.
        $actual = $tagged->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function test_it_gets_tag_for_model()
    {
        $tagged = $this->seeded->tagged[0];
        $expected = $this->seeded->tags[$tagged->tag_slug];

        $actual = $this->repo->tagFor($tagged);

        $this->assertInstanceOf(Tag::class, $actual);
        $this->assertEquals($expected->toArray(), $actual->toArray());
    }

    public function test_it_gets_taggable_for_model()
    {
        $tagged = $this->seeded->tagged[0];         // First Tag on "Spark & Storage" post
        /** @var \Dan\Tagging\Testing\Integration\Setup\Post $expected */
        $expected = $this->seeded->posts['spark'];  // "Spark & Storage" Post

        $actual = $this->repo->taggableFor($tagged);
        $this->assertInstanceOf(Post::class, $actual);

        $expected = $expected->toArray();
        $actual = $actual->toArray();

        unset($actual['created_at'], $actual['updated_at'], $expected['created_at'], $expected['updated_at']);
        $this->assertEquals($expected, $actual);
    }

    public function test_it_gets_users_ids_for_model()
    {
        $tagged = $this->seeded->tagged[0];         // "Spark & Storage" Post
        /** @var \Dan\Tagging\Testing\Integration\Setup\Post $expected */

        $expected = TaggedUser::where('tagged_id', $tagged->id)
            ->pluck('user_id')->all();

        $actual = $this->repo->usersIdsFor($tagged);

        $this->assertEquals($expected, $actual);
    }

    public function test_it_gets_users_for_model()
    {
        $tagged = $this->seeded->tagged[0];         // "Spark & Storage" Post
        /** @var \Dan\Tagging\Testing\Integration\Setup\Post $expected */

        $expected = TaggedUser::where('tagged_id', $tagged->id)
            ->pluck('user_id')->all();

        $actual = $this->repo->usersFor($tagged);
        $this->assertInstanceOf(Collection::class, $actual);

        $first = $actual->first();
        $this->assertInstanceOf($this->repo->tagUtil()->userModelString(), $first);

        $actual = $actual->pluck('id')->all();

        $this->assertEquals($expected, $actual);
    }

    public function test_it_recalculates_users_count_for_model()
    {
        // Only Alice has tagged the "Thought Police" Post as "Best Practices"
        $tagged = Tagged::where('tag_slug', 'best-practices')->first();
        $this->assertEquals(1, $tagged->users_count);

        // Now Bob will tag the "Thought Police" Post as "Best Practices"
        factory(\Dan\Tagging\Models\TaggedUser::class)->create([
            'tagged_id' => $tagged->getKey(),
            'user_id' => $this->seeded->users['bob']->getKey()
        ]);

        // Verify count is 2 after recalculating
        $this->repo->recalculateFor($tagged);
        $tagged = Tagged::where('tag_slug', 'best-practices')->first();
        $this->assertEquals(2, $tagged->users_count);
    }

}
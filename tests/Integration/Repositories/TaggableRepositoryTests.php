<?php

namespace Dan\Tagging\Testing\Integration\Repositories;

use Dan\Tagging\Models\Tag;
use Dan\Tagging\Models\Tagged;
use Dan\Tagging\Models\TaggedUser;
use Illuminate\Database\Eloquent\Collection;
use Dan\Tagging\Testing\Integration\Setup\User;
use Dan\Tagging\Repositories\Tagged\TaggedInterface;
use Dan\Tagging\Testing\Integration\IntegrationTestCase;
use Dan\Tagging\Testing\Integration\Setup\Posts\PostsInterface;

/**
 * Class TaggableRepositoryTests
 *
 * @see \IntegrationTestsSeeder
 */
class TaggableRepositoryTests extends IntegrationTestCase
{

    /** @var \Dan\Tagging\Testing\Integration\Setup\Posts\PostsRepository $repo */
    protected $repo;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->repo = app(PostsInterface::class);
    }
    
    public function test_it_tags_a_taggable_for_a_user()
    {
        $lumen = $this->seeded->posts['lumen'];
        $charlie = $this->seeded->users['charlie'];
        $this->repo->tagForUser($lumen, $charlie, 'Taco');

        $class = get_class($lumen);
        $id = $lumen->getKey();

        /** @var \Dan\Tagging\Repositories\Tagged\TaggedRepository $tagged */
        $tagged = app(TaggedInterface::class);

        $expected = $tagged->findByModelKeySlug($class, $id, 'lumen')->toArray();
        $actual = $tagged->findByModelKeySlug($class, $id, 'taco')->toArray();

        $this->assertEquals($expected['taggable_type'], $actual['taggable_type']);
        $this->assertEquals($expected['taggable_id'], $actual['taggable_id']);
    }

    public function test_it_untags_a_taggable_for_a_user()
    {
        $lumen = $this->seeded->posts['lumen'];
        $alice = $this->seeded->users['alice'];
        $class = get_class($lumen);
        $id = $lumen->getKey();

        /** @var \Dan\Tagging\Repositories\Tagged\TaggedRepository $tagged */
        $taggedRepo = app(TaggedInterface::class);

        // Verify the "testing" tag exists on the Post.
        $tagged = $taggedRepo->findByModelKeySlug($class, $id, 'testing');
        $expected = TaggedUser::where('tagged_id', $tagged->getKey())
            ->where('user_id', $alice->getKey())
            ->first()
            ->toArray();
        $this->assertNotEmpty($expected);

        // Delete the "lumen" tag on the Post for Charlie.
        $this->repo->untagForUser($lumen, $alice, 'testing');

        // Verify the "testing" tag DOES NOT exist on the Post.
        $tagged = $taggedRepo->findByModelKeySlug($class, $id, 'testing');
        $actual = TaggedUser::where('tagged_id', $tagged->getKey())
            ->where('user_id', $alice->getKey())
            ->first();
        $this->assertNull($actual);
    }

    public function test_it_retags_a_taggable_for_a_user()
    {
        $lumen = $this->seeded->posts['lumen'];
        $alice = $this->seeded->users['alice'];
        $class = get_class($lumen);
        $id = $lumen->getKey();

        /** @var \Dan\Tagging\Repositories\Tagged\TaggedRepository $taggedRepo */
        $taggedRepo = app(TaggedInterface::class);

        // Verify the "testing" tag exists on the Post.
        $tagged = $taggedRepo->findByModelKeySlug($class, $id, 'testing');
        $expected = TaggedUser::where('tagged_id', $tagged->getKey())
            ->where('user_id', $alice->getKey())
            ->first()
            ->toArray();
        $this->assertNotEmpty($expected);

        // Store "testing" Tagged to check against "taco" at the end.
        $expected = $tagged->toArray();

        // Delete the "lumen" tag on the Post for Charlie.
        $this->repo->retagForUser($lumen, $alice, 'taco');

        // Verify the "testing" tag DOES NOT exist on the Post.
        $tagged = $taggedRepo->findByModelKeySlug($class, $id, 'testing');
        $actual = TaggedUser::where('tagged_id', $tagged->getKey())
            ->where('user_id', $alice->getKey())
            ->first();
        $this->assertNull($actual);

        // Verify the "taco" tag exists on the Post.
        $actual = $taggedRepo->findByModelKeySlug($class, $id, 'taco')->toArray();
        $this->assertEquals($expected['taggable_type'], $actual['taggable_type']);
        $this->assertEquals($expected['taggable_id'], $actual['taggable_id']);
    }

    public function test_it_gets_tags_for_taggable()
    {
        $actual = $this->repo->tagsFor($this->seeded->posts['lumen']);
        $this->assertInstanceOf(Collection::class, $actual);
        $this->assertInstanceOf(Tag::class, $actual->first());
        $this->assertEquals(
            ['benchmarks', 'frameworks', 'lumen', 'testing'],
            $actual->pluck('slug')->all()
        );
    }

    public function test_it_gets_tag_names_for_taggable()
    {
        $this->assertEquals(
            ['Benchmarks', 'Frameworks', 'Lumen', 'Testing'],
            $this->repo->tagNamesFor($this->seeded->posts['lumen'])
        );
    }

    public function test_it_gets_tag_slugs_for_taggable()
    {
        $this->assertEquals(
            ['benchmarks', 'frameworks', 'lumen', 'testing'],
            $this->repo->tagSlugsFor($this->seeded->posts['lumen'])
        );
    }

    public function test_it_gets_tagged_for_taggable()
    {
        $tagged = $this->repo->taggedFor($this->seeded->posts['lumen']);
        $this->assertInstanceOf(Collection::class, $tagged);
        $this->assertInstanceOf(Tagged::class, $tagged->first());
        $this->assertEquals(
            ['benchmarks', 'frameworks', 'lumen', 'testing'],
            $tagged->pluck('tag_slug')->all()
        );
    }

    public function test_it_gets_tagged_col_for_taggable()
    {
        $this->assertEquals(
            ['benchmarks', 'frameworks', 'lumen', 'testing'],
            $this->repo->taggedColFor($this->seeded->posts['lumen'])
        );
    }

    public function test_it_gets_tagged_ids_for_taggable()
    {
        $taggedIds = $this->repo->taggedIdsFor($this->seeded->posts['lumen']);
        sort($taggedIds);
        $this->assertEquals(range(6,9), $taggedIds);
    }

    public function test_it_gets_tagged_for_taggable_and_user()
    {
        $spark = $this->seeded->posts['spark'];
        $alice = $this->seeded->users['alice'];
        $tagged = $this->repo->taggedForUser($spark, $alice);
        $this->assertInstanceOf(Collection::class, $tagged);
        $this->assertInstanceOf(Tagged::class, $tagged->first());
        $this->assertEquals(
            ['laravel', 'spark', 'storage'],
            $tagged->pluck('tag_slug')->all()            
        );
    }

    public function test_it_gets_tagged_column_for_taggable_and_user()
    {
        $spark = $this->seeded->posts['spark'];
        $alice = $this->seeded->users['alice'];
        $slugs = $this->repo->taggedColForUser($spark, $alice);
        $this->assertEquals(['laravel', 'spark', 'storage'], $slugs);
    }

    public function test_it_gets_tagged_ids_for_taggable_and_user()
    {
        $spark = $this->seeded->posts['spark'];
        $alice = $this->seeded->users['alice'];
        $this->assertEquals(
            range(1,3),
            $this->repo->taggedIdsForUser($spark, $alice)            
        );
    }

    public function test_it_gets_tags_for_taggable_and_user()
    {
        $spark = $this->seeded->posts['spark'];
        $alice = $this->seeded->users['alice'];
        $tags = $this->repo->tagsForUser($spark, $alice);
        $this->assertInstanceOf(Collection::class, $tags);
        $this->assertInstanceOf(Tag::class, $tags->first());
        $this->assertEquals(
            ['laravel', 'spark', 'storage'],
            $tags->pluck('slug')->all()
        );
    }

    public function test_it_gets_tag_slugs_for_taggable_and_user()
    {
        $spark = $this->seeded->posts['spark'];
        $alice = $this->seeded->users['alice'];
        $this->assertEquals(
            ['laravel', 'spark', 'storage'],
            $this->repo->tagSlugsForUser($spark, $alice)
        );
    }

    public function test_it_gets_user_ids_who_tagged()
    {
        $spark = $this->seeded->posts['spark'];
        $this->assertEquals(
            range(1, 3),
            $this->repo->userIdsWhoTagged($spark)
        );
    }

    public function test_it_gets_users_who_tagged()
    {
        $spark = $this->seeded->posts['spark'];
        $users = $this->repo->usersWhoTagged($spark);
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertInstanceOf(User::class, $users->first());
        $this->assertEquals(
            ['Alice', 'Bob', 'Charlie'],
            $users->pluck('name')->all()
        );
    }

    public function test_it_is_tagged_by_model_user()
    {
        $lumen = $this->seeded->posts['lumen'];
        $alice = $this->seeded->users['alice'];
        $charlie = $this->seeded->users['charlie'];
        $this->assertTrue($this->repo->isTaggedByUser($lumen, $alice));
        $this->assertFalse($this->repo->isTaggedByUser($lumen, $charlie));
        $this->assertTrue($this->repo->isTaggedByUser($lumen, 1));
        $this->assertFalse($this->repo->isTaggedByUser($lumen, 3));
    }

    public function data_provider_test_it_is_tagged_by_with_model_user_tags()
    {
        return [
            ['lumen'],
            ['lumen,testing'],
            [['lumen', 'testing']],
        ];
    }

    /**
     * @dataProvider data_provider_test_it_is_tagged_by_with_model_user_tags
     * @param mixed$tags
     */
    public function test_it_is_tagged_by_with_model_user_tags($tags)
    {
        $lumen = $this->seeded->posts['lumen'];
        $alice = $this->seeded->users['alice'];
        $charlie = $this->seeded->users['charlie'];
        $this->assertTrue($this->repo->isTaggedByUserWith($lumen, $alice, $tags));
        $this->assertFalse($this->repo->isTaggedByUserWith($lumen, $charlie, $tags));
        $this->assertTrue($this->repo->isTaggedByUserWith($lumen, 1, $tags));
        $this->assertFalse($this->repo->isTaggedByUserWith($lumen, 3, $tags));
    }

}
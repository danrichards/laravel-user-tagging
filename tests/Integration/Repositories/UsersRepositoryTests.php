<?php

namespace Dan\Tagging\Testing\Integration\Repositories;

use Dan\Tagging\Models\Tag;
use Dan\Tagging\Models\Tagged;
use Dan\Tagging\Testing\Integration\Setup\Post;
use Dan\Tagging\Testing\Integration\Setup\Users\UsersInterface;
use Dan\Tagging\Testing\Integration\IntegrationTestCase;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * Class UsersRepositoryTests
 *
 * @see \IntegrationTestsSeeder
 */
class UsersRepositoryTests extends IntegrationTestCase
{
    
    /** @var \Dan\Tagging\Testing\Integration\Setup\Users\UsersRepository $repo */
    protected $repo;
    
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->repo = app(UsersInterface::class);
    }
    
    public function test_it_gets_tags_ids_for_user()
    {
        $charlie = $this->seeded->users['charlie'];
        $ids = $this->repo->tagsIdsFor($charlie);
        $slugs = Tag::findMany($ids)->pluck('slug')->all();
        $this->assertEquals(['laravel', 'spark', 'announcements'], $slugs);
    }

    public function test_it_gets_tags_for_user()
    {
        $charlie = $this->seeded->users['charlie'];
        $tags = $this->repo->tagsFor($charlie);
        $this->assertInstanceOf(EloquentCollection::class, $tags);
        $this->assertInstanceOf(Tag::class, $tags->first());
        $slugs = $tags->pluck('slug')->all();
        $this->assertEquals(['laravel', 'spark', 'announcements'], $slugs);
    }

    public function test_it_gets_tags_for_user_with_counts()
    {
        $alice = $this->seeded->users['alice'];
        $tagCounts = $this->repo->tagsForUserWithCounts($alice);
        $this->assertInstanceOf(Collection::class, $tagCounts);
        
        $this->assertEquals([
                'slug' => 'metaphors',
                'name' => 'Metaphors',
                'count' => '2'
            ],
            $tagCounts->first()
        );

        $this->assertEquals([
                'metaphors',
                'attitude',
                'benchmarks',
                'best-practices',
                'heroes',
                'inspiration',
                'laravel',
                'lumen',
                'php',
                'programming',
                'solid',
                'spark',
                'storage',
                'testing'
            ],
            $tagCounts->pluck('slug')->all()
        );
    }

    public function test_it_gets_tagged_column_for_user()
    {
        $charlie = $this->seeded->users['charlie'];
        $slugs = $this->repo->taggedColFor($charlie, 'tag_slug');
        $names = $this->repo->taggedColFor($charlie, 'tag_name');
        $this->assertEquals(['laravel', 'spark', 'announcements'], $slugs);
        $this->assertEquals(['Laravel', 'Spark', 'Announcements'], $names);
    }

    public function test_it_gets_tagged_ids_for_user()
    {
        $charlie = $this->seeded->users['charlie'];
        $ids = $this->repo->taggedIdsFor($charlie);
        $slugs = Tagged::findMany($ids)->pluck('tag_slug')->all();
        $this->assertEquals(['laravel', 'spark', 'announcements'], $slugs);
    }

    public function test_it_gets_tagged_for_user()
    {
        $charlie = $this->seeded->users['charlie'];
        $tagged = $this->repo->taggedFor($charlie);
        $this->assertInstanceOf(EloquentCollection::class, $tagged);
        $this->assertInstanceOf(Tagged::class, $tagged->first());
        $slugs = $tagged->pluck('tag_slug')->all();
        $this->assertEquals(['laravel', 'spark', 'announcements'], $slugs);
    }
    
    public function test_it_gets_tagged_taggables_for_user()
    {
        $charlie = $this->seeded->users['charlie'];
        $taggables = $this->repo->taggedTaggablesFor($charlie);
        $this->assertInstanceOf(EloquentCollection::class, $taggables);
        $this->assertInstanceOf(Post::class, $taggables->first());
        $spark = $this->seeded->posts['spark'];
        $this->assertEquals($spark->toArray(), $taggables->first()->toArray());
    }

}
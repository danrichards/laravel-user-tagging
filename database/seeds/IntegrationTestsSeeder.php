<?php

use Dan\Tagging\Testing\Integration\Setup\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Class IntegrationTestsSeeder
 */
class IntegrationTestsSeeder extends Seeder
{

    /** @var array $users */
    public $users = [];

    /** @var array $posts */
    public $posts = [];
    
    /** @var array $tags */
    public $tags = [];
    
    /** @var array $tagged */
    public $tagged = [];

    /** @var array $taggedUser */
    public $taggedUser = [];

    /**
     * Run the database seeds.
     *
     * @return $this
     */
    public function run()
    {
        $this->users();

        $this->posts();

        $this->tagging();

        return $this;
    }

    /**
     * Make some example users to tag stuff.
     *
     * Alice, Bob, and Charlie
     */
    public function users()
    {
        // Data for the `users` table

        $this->users['alice'] = factory(\Dan\Tagging\Testing\Integration\Setup\User::class)->create([
            'name' => 'Alice',
            'email' => 'alice@example.com'
        ]);

        $this->users['bob'] = factory(\Dan\Tagging\Testing\Integration\Setup\User::class)->create([
            'name' => 'Bob',
            'email' => 'bob@example.com'
        ]);

        $this->users['charlie'] = factory(\Dan\Tagging\Testing\Integration\Setup\User::class)->create([
            'name' => 'Charlie',
            'email' => 'charlie@example.com'
        ]);
    }

    /**
     * Example posts from Taylor's blog our users will tag.
     *
     * Spark & Storage
     * How Lumen Is Benchmarked
     * Thought Police
     * Starting Positive
     * PHP Developers Who Have Inspired Me
     */
    public function posts()
    {
        // Data for the `posts` table

        $this->posts['spark'] = factory(Post::class)->create([
            'title' => 'Spark & Storage',
            'body' => 'I\'ve been wanting to start blogging more frequent development updates and now is a great time to start!'
        ]);

        $this->posts['lumen'] = factory(Post::class)->create([
            'title' => 'How Lumen Is Benchmarked',
            'body' => 'I\'ve received a few tweets asking me to demonstrate how Lumen is benchmarked against Silex and Slim.'
        ]);

        $this->posts['thought'] = factory(Post::class)->create([
            'title' => 'Thought Police',
            'body' => 'Programmers are an interesting bunch – brimming with passion and pride in the shadows of our mansions. They\'re beautiful, our mansions of bits and bytes. But, do you know what gets us worked up? The neighbor\'s yard – and their kids… Lord knows they need a tutor. They probably don\'t even code to an interface. Gross. I mean, it\'s dangerous.'
        ]);

        $this->posts['positive'] = factory(Post::class)->create([
            'title' => 'Starting Positive',
            'body' => 'In the past, fishermen have used the color of the morning sky to predict if the day\'s weather will be suitable for sailing. Like the fishermen, I\'ve found that the day\'s beginnings paint a preview of its course. Start positive, and it\'s easier to ride the good vibes for the rest of the day.'
        ]);

        $this->posts['inspired'] = factory(Post::class)->create([
            'title' => 'PHP Developers Who Have Inspired Me',
            'body' => 'Tonight I want to write a quick post and mention a few PHP developers who have inspired me lately. I\'ll name them and share a few reflections on how they have inspired me over the past few months.'
        ]);
    }

    /**
     * Alice will tag:
     *
     * Spark & Storage                          laravel, spark, storage
     * How Lumen Is Benchmarked                 lumen, testing, benchmarks
     * Thought Police                           solid, best-practices, metaphors
     * Starting Positive                        programming, attitude, metaphors
     * PHP Developers Who Have Inspired Me      php, heroes, inspiration
     *
     * Bob will tag:
     *
     * Spark & Storage:                         laravel, spark, payment-gateways
     * How Lumen Is Benchmarked                 lumen, benchmarks, frameworks
     * Thought Police                           programming, solid, metaphors
     *
     * Charlie will tag:
     *
     * Spark & Storage:                         laravel, spark, announcements
     */
    public function tagging()
    {
        // Data for the `tagging_tags` table

        $tags['laravel'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Laravel', 'slug' => 'laravel']);
        $tags['spark'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Spark', 'slug' => 'spark']);
        $tags['storage'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Storage', 'slug' => 'storage']);
        $tags['lumen'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Lumen', 'slug' => 'lumen']);
        $tags['testing'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Testing', 'slug' => 'testing']);
        $tags['benchmarks'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Benchmarks', 'slug' => 'benchmarks']);
        $tags['solid'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'SOLID', 'slug' => 'solid']);
        $tags['best-practices'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Best Practices', 'slug' => 'best-practices']);
        $tags['metaphors'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Metaphors', 'slug' => 'metaphors']);
        $tags['programming'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Programming', 'slug' => 'programming']);
        $tags['attitude'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Attitude', 'slug' => 'attitude']);
        $tags['php'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'PHP', 'slug' => 'php']);
        $tags['heroes'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Heroes', 'slug' => 'heroes']);
        $tags['inspiration'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Inspiration', 'slug' => 'inspiration']);
        $tags['payment-gateways'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Payment Gateways', 'slug' => 'payment-gateways']);
        $tags['frameworks'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Frameworks', 'slug' => 'frameworks']);
        $tags['announcements'] = factory(\Dan\Tagging\Models\Tag::class)->create(['name' => 'Announcements', 'slug' => 'announcements']);

        // Data for the `tagging_tagged` table
        
        $spark['laravel'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['spark']->getKey(), 'taggable_type' => get_class($this->posts['spark']), 'tag_name' => $tags['laravel']->name, 'tag_slug' => $tags['laravel']->slug]);
        $spark['spark'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['spark']->getKey(), 'taggable_type' => get_class($this->posts['spark']), 'tag_name' => $tags['spark']->name, 'tag_slug' => $tags['spark']->slug]);
        $spark['storage'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['spark']->getKey(), 'taggable_type' => get_class($this->posts['spark']), 'tag_name' => $tags['storage']->name, 'tag_slug' => $tags['storage']->slug]);
        $spark['payment-gateways'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['spark']->getKey(), 'taggable_type' => get_class($this->posts['spark']), 'tag_name' => $tags['payment-gateways']->name, 'tag_slug' => $tags['payment-gateways']->slug]);
        $spark['announcements'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['spark']->getKey(), 'taggable_type' => get_class($this->posts['spark']), 'tag_name' => $tags['announcements']->name, 'tag_slug' => $tags['announcements']->slug]);

        $lumen['lumen'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['lumen']->getKey(), 'taggable_type' => get_class($this->posts['lumen']), 'tag_name' => $tags['lumen']->name, 'tag_slug' => $tags['lumen']->slug]);
        $lumen['testing'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['lumen']->getKey(), 'taggable_type' => get_class($this->posts['lumen']), 'tag_name' => $tags['testing']->name, 'tag_slug' => $tags['testing']->slug]);
        $lumen['benchmarks'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['lumen']->getKey(), 'taggable_type' => get_class($this->posts['lumen']), 'tag_name' => $tags['benchmarks']->name, 'tag_slug' => $tags['benchmarks']->slug]);
        $lumen['frameworks'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['lumen']->getKey(), 'taggable_type' => get_class($this->posts['lumen']), 'tag_name' => $tags['frameworks']->name, 'tag_slug' => $tags['frameworks']->slug]);
        
        $thought['solid'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['thought']->getKey(), 'taggable_type' => get_class($this->posts['thought']), 'tag_name' => $tags['solid']->name, 'tag_slug' => $tags['solid']->slug]);
        $thought['best-practices'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['thought']->getKey(), 'taggable_type' => get_class($this->posts['thought']), 'tag_name' => $tags['best-practices']->name, 'tag_slug' => $tags['best-practices']->slug]);
        $thought['metaphors'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['thought']->getKey(), 'taggable_type' => get_class($this->posts['thought']), 'tag_name' => $tags['metaphors']->name, 'tag_slug' => $tags['metaphors']->slug]);
        $thought['programming'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['thought']->getKey(), 'taggable_type' => get_class($this->posts['thought']), 'tag_name' => $tags['programming']->name, 'tag_slug' => $tags['programming']->slug]);

        $positive['programming'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['positive']->getKey(), 'taggable_type' => get_class($this->posts['positive']), 'tag_name' => $tags['programming']->name, 'tag_slug' => $tags['programming']->slug]);
        $positive['attitude'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['positive']->getKey(), 'taggable_type' => get_class($this->posts['positive']), 'tag_name' => $tags['attitude']->name, 'tag_slug' => $tags['attitude']->slug]);
        $positive['metaphors'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['positive']->getKey(), 'taggable_type' => get_class($this->posts['positive']), 'tag_name' => $tags['metaphors']->name, 'tag_slug' => $tags['metaphors']->slug]);
        
        $inspired['php'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['inspired']->getKey(), 'taggable_type' => get_class($this->posts['inspired']), 'tag_name' => $tags['php']->name, 'tag_slug' => $tags['php']->slug]);
        $inspired['heroes'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['inspired']->getKey(), 'taggable_type' => get_class($this->posts['inspired']), 'tag_name' => $tags['heroes']->name, 'tag_slug' => $tags['heroes']->slug]);
        $inspired['inspiration'] = factory(\Dan\Tagging\Models\Tagged::class)->create(['taggable_id' => $this->posts['inspired']->getKey(), 'taggable_type' => get_class($this->posts['inspired']), 'tag_name' => $tags['inspiration']->name, 'tag_slug' => $tags['inspiration']->slug]);

        // Data for the `tagging_tagged_user` table

        /**
         * Alice will tag:
         *
         * Spark & Storage                          laravel, spark, storage
         * How Lumen Is Benchmarked                 lumen, testing, benchmarks
         * Thought Police                           solid, best-practices, metaphors
         * Starting Positive                        programming, attitude, metaphors
         * PHP Developers Who Have Inspired Me      php, heroes, inspiration
         */
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['laravel']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['spark']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['storage']->getKey(), 'user_id' => $this->users['alice']->getKey()]);

        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $lumen['lumen']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $lumen['testing']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $lumen['benchmarks']->getKey(), 'user_id' => $this->users['alice']->getKey()]);

        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $thought['solid']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $thought['best-practices']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $thought['metaphors']->getKey(), 'user_id' => $this->users['alice']->getKey()]);

        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $positive['programming']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $positive['attitude']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $positive['metaphors']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $inspired['php']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $inspired['heroes']->getKey(), 'user_id' => $this->users['alice']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $inspired['inspiration']->getKey(), 'user_id' => $this->users['alice']->getKey()]);

        /**
         * Bob will tag:
         *
         * Spark & Storage:                         laravel, spark, payment-gateways
         * How Lumen Is Benchmarked                 lumen, benchmarks, frameworks
         * Thought Police                           programming, solid, metaphors
         */
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['laravel']->getKey(), 'user_id' => $this->users['bob']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['spark']->getKey(), 'user_id' => $this->users['bob']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['payment-gateways']->getKey(), 'user_id' => $this->users['bob']->getKey()]);

        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $lumen['lumen']->getKey(), 'user_id' => $this->users['bob']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $lumen['benchmarks']->getKey(), 'user_id' => $this->users['bob']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $lumen['frameworks']->getKey(), 'user_id' => $this->users['bob']->getKey()]);

        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $thought['programming']->getKey(), 'user_id' => $this->users['bob']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $thought['solid']->getKey(), 'user_id' => $this->users['bob']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $thought['metaphors']->getKey(), 'user_id' => $this->users['bob']->getKey()]);

        /**
         * Charlie will tag:
         *
         * Spark & Storage:                         laravel, spark, announcements
         */
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['laravel']->getKey(), 'user_id' => $this->users['charlie']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['spark']->getKey(), 'user_id' => $this->users['charlie']->getKey()]);
        $this->taggedUser[] = factory(\Dan\Tagging\Models\TaggedUser::class)->create(['tagged_id' => $spark['announcements']->getKey(), 'user_id' => $this->users['charlie']->getKey()]);

        // Calculate the number of users who tagged a Model, with a specific tag, for each tag.
        $tagged = array_merge(
            array_values($spark),
            array_values($lumen),
            array_values($thought),
            array_values($positive),
            array_values($inspired)
        );

        // Calculate how many users have tagged each model / tag
        foreach($tagged as &$tg) {
            /** @var \Dan\Tagging\Models\Tagged $tg */
            $tg->recalculate();
        }

        // Calculate how many times a tag has been used.
        foreach($tags as $tag) {
            /** @var \Dan\Tagging\Models\Tag $t */
            $tag->recalculate();
        }
        $this->tags = $tags;

        $this->tagged = $tagged;
    }

}
<?php

namespace Tests\Feature\Api\Site;

use App\Models\Advertise;
use App\Models\Category;
use App\Models\Club;
use App\Models\Comment;
use App\Models\Country;
use App\Models\FavoriteClub;
use App\Models\League;
use App\Models\Live;
use App\Models\Page;
use App\Models\Post;
use App\Models\Sport;
use App\Models\Step;
use App\Models\Tag;
use App\Models\User;
use App\Services\TelegramNotificationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SiteEndpointsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TelegramNotificationService::class, function ($mock) {
            $mock->shouldReceive('sendNotification')->andReturnNull();
        });

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true], 200),
        ]);

        // ClubRepository::getInfo filters categories by club_id (present in prod, missing from migrations).
        if (! Schema::hasColumn('categories', 'club_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedBigInteger('club_id')->nullable()->index();
            });
        }
    }

    protected function createAuthor(): User
    {
        return User::factory()->create([
            'status' => 1,
            'role_id' => 2,
            'nickname' => 'author_'.uniqid(),
            'password' => bcrypt('Password1!'),
        ]);
    }

    protected function createPost(User $author, array $attributes = []): Post
    {
        return Post::factory()->create(array_merge([
            'user_id' => $author->id,
            'status' => 1,
            'type' => 0,
            'special' => 0,
            'image' => ['https://example.com/post.jpg'],
        ], $attributes));
    }

    protected function createSport(User $user): Sport
    {
        return Sport::query()->create([
            'title' => 'Football',
            'alias_title' => 'football',
            'status' => 1,
            'user_id' => $user->id,
            'image' => 'https://example.com/sport.jpg',
        ]);
    }

    protected function createCountry(User $user): Country
    {
        return Country::query()->create([
            'title' => 'Iran',
            'alias_title' => 'iran',
            'status' => 1,
            'user_id' => $user->id,
            'image' => 'https://example.com/country.jpg',
        ]);
    }

    protected function createClub(User $user, Sport $sport, Country $country, array $attributes = []): Club
    {
        return Club::query()->create(array_merge([
            'title' => 'Test Club',
            'alias_title' => 'test-club',
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'status' => 1,
            'user_id' => $user->id,
            'image' => 'https://example.com/club.jpg',
        ], $attributes));
    }

    public function test_suggested_posts_returns_ok(): void
    {
        $author = $this->createAuthor();
        $category = Category::factory()->create([
            'status' => 1,
            'menu' => 0,
            'user_id' => $author->id,
        ]);
        $post = $this->createPost($author);
        $post->categories()->attach($category->id);

        $this->getJson('/api/suggested-posts')
            ->assertOk()
            ->assertJsonStructure(['latest', 'challenged', 'popular', 'specialPosts', 'specialVideos']);
    }

    public function test_author_posts_returns_ok(): void
    {
        $author = $this->createAuthor();
        $this->createPost($author, ['title' => 'Author exclusive post']);

        $this->getJson('/api/author-posts/'.$author->id)
            ->assertOk();
    }

    public function test_posts_index_returns_ok(): void
    {
        $author = $this->createAuthor();
        $category = Category::factory()->create([
            'status' => 1,
            'menu' => 0,
            'user_id' => $author->id,
        ]);
        $post = $this->createPost($author);
        $post->categories()->attach($category->id);

        $this->getJson('/api/posts')
            ->assertOk()
            ->assertJsonStructure(['latest', 'categories', 'challenged', 'popular', 'specialPosts', 'specialVideos', 'posts']);
    }

    public function test_post_info_returns_ok(): void
    {
        $author = $this->createAuthor();
        $post = $this->createPost($author, ['title' => 'Single post info']);

        $this->getJson('/api/post/'.$post->id)
            ->assertOk()
            ->assertJsonPath('id', $post->id)
            ->assertJsonPath('title', 'Single post info');
    }

    public function test_archive_by_category_returns_ok(): void
    {
        $author = $this->createAuthor();
        $category = Category::factory()->create([
            'status' => 1,
            'menu' => 0,
            'user_id' => $author->id,
            'title' => 'Archive Category',
        ]);
        $post = $this->createPost($author);
        $post->categories()->attach($category->id);

        $this->getJson('/api/archive/'.$category->id)
            ->assertOk()
            ->assertJsonStructure(['data', 'category']);
    }

    public function test_leagues_index_returns_ok(): void
    {
        $user = $this->createAuthor();
        $sport = $this->createSport($user);
        $country = $this->createCountry($user);

        League::query()->create([
            'title' => 'Premier League',
            'alias_title' => 'premier',
            'status' => 1,
            'table_id' => 1,
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'user_id' => $user->id,
            'type' => 1,
            'priority' => 1,
            'image' => 'https://example.com/league.jpg',
        ]);

        $this->getJson('/api/leagues')
            ->assertOk();
    }

    public function test_league_info_returns_ok(): void
    {
        $user = $this->createAuthor();
        $sport = $this->createSport($user);
        $country = $this->createCountry($user);

        $league = League::query()->create([
            'title' => 'Info League',
            'alias_title' => 'info-league',
            'status' => 1,
            'table_id' => 1,
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'user_id' => $user->id,
            'type' => 1,
            'priority' => 1,
        ]);

        Step::query()->create([
            'title' => 'Week 1',
            'league_id' => $league->id,
            'user_id' => $user->id,
            'priority' => 1,
            'current' => 1,
            'show_table' => 1,
            'status' => 1,
        ]);

        $this->getJson('/api/leagues/'.$league->id)
            ->assertOk()
            ->assertJsonStructure(['steps', 'matches', 'clubs']);
    }

    public function test_step_info_returns_ok(): void
    {
        $user = $this->createAuthor();
        $sport = $this->createSport($user);
        $country = $this->createCountry($user);

        $league = League::query()->create([
            'title' => 'Step League',
            'status' => 1,
            'table_id' => 1,
            'sport_id' => $sport->id,
            'country_id' => $country->id,
            'user_id' => $user->id,
            'type' => 1,
            'priority' => 1,
        ]);

        $step = Step::query()->create([
            'title' => 'Final Step',
            'league_id' => $league->id,
            'user_id' => $user->id,
            'priority' => 1,
            'current' => 1,
            'show_table' => 1,
            'status' => 1,
        ]);

        $this->getJson('/api/step/'.$step->id)
            ->assertOk()
            ->assertJsonStructure(['matches', 'clubs']);
    }

    public function test_lives_index_returns_ok(): void
    {
        $user = $this->createAuthor();

        Live::query()->create([
            'title' => 'Live Match',
            'teams' => 'A vs B',
            'date' => '2026-10-05 20:00',
            'link' => 'https://example.com/live',
            'info' => 'Kickoff',
            'status' => 1,
            'priority' => 1,
            'user_id' => $user->id,
        ]);

        $this->getJson('/api/lives')
            ->assertOk();
    }

    public function test_advertise_index_returns_ok(): void
    {
        $user = $this->createAuthor();

        Advertise::query()->create([
            'title' => 'Banner Ad',
            'link' => 'https://example.com/ad',
            'image' => 'https://example.com/ad.jpg',
            'status' => 1,
            'place_id' => 1,
            'user_id' => $user->id,
        ]);

        $this->getJson('/api/advertise')
            ->assertOk();
    }

    public function test_active_categories_returns_ok(): void
    {
        $user = $this->createAuthor();

        Category::factory()->create([
            'status' => 1,
            'menu' => 1,
            'user_id' => $user->id,
            'title' => 'Menu Category',
        ]);

        $this->getJson('/api/active-categories')
            ->assertOk();
    }

    public function test_popular_categories_returns_ok(): void
    {
        $user = $this->createAuthor();
        $category = Category::factory()->create([
            'status' => 1,
            'menu' => 0,
            'user_id' => $user->id,
            'title' => 'Popular Category',
        ]);
        $post = $this->createPost($user);
        $post->categories()->attach($category->id);

        $this->getJson('/api/popular-categories')
            ->assertOk();
    }

    public function test_team_categories_returns_ok(): void
    {
        $user = $this->createAuthor();

        Category::factory()->create([
            'status' => 1,
            'menu' => 0,
            'user_id' => $user->id,
            'title' => 'Team Category',
        ]);

        $this->getJson('/api/team-categories')
            ->assertOk();
    }

    public function test_tags_random_returns_ok(): void
    {
        Tag::factory()->create(['title' => 'RandomTag']);

        $this->getJson('/api/tags-random')
            ->assertOk();
    }

    public function test_tag_posts_returns_ok(): void
    {
        $author = $this->createAuthor();
        $tag = Tag::factory()->create(['title' => 'FootballTag']);
        $post = $this->createPost($author);
        $post->tags()->attach($tag->id);

        $this->getJson('/api/tag/'.$tag->id)
            ->assertOk();
    }

    public function test_club_info_returns_ok(): void
    {
        $user = $this->createAuthor();
        $sport = $this->createSport($user);
        $country = $this->createCountry($user);
        $club = $this->createClub($user, $sport, $country, [
            'title' => 'Esteghlal',
        ]);

        $category = Category::factory()->create([
            'status' => 1,
            'menu' => 0,
            'user_id' => $user->id,
            'club_id' => $club->id,
            'title' => 'Esteghlal Category',
        ]);

        $post = $this->createPost($user);
        $post->categories()->attach($category->id);

        $this->getJson('/api/club/'.$club->id)
            ->assertOk()
            ->assertJsonStructure(['tag', 'info']);
    }

    public function test_club_followers_returns_ok(): void
    {
        $user = $this->createAuthor();
        $follower = User::factory()->create([
            'status' => 1,
            'level' => 0,
            'role_id' => 4,
            'password' => bcrypt('Password1!'),
        ]);
        $sport = $this->createSport($user);
        $country = $this->createCountry($user);
        $club = $this->createClub($user, $sport, $country);

        FavoriteClub::query()->create([
            'user_id' => $follower->id,
            'club_id' => $club->id,
        ]);

        $this->postJson('/api/club/'.$club->id.'/followers', [
            'page' => 1,
            'count' => 20,
        ])
            ->assertOk();
    }

    public function test_club_followers_validation_fails_for_invalid_sort(): void
    {
        $user = $this->createAuthor();
        $sport = $this->createSport($user);
        $country = $this->createCountry($user);
        $club = $this->createClub($user, $sport, $country);

        $this->postJson('/api/club/'.$club->id.'/followers', [
            'sort' => 'invalid',
        ])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_search_posts_returns_ok(): void
    {
        $author = $this->createAuthor();
        $this->createPost($author, [
            'title' => 'Searchable Unique Title XYZ',
        ]);

        $this->postJson('/api/search', [
            'search' => 'Searchable Unique',
        ])
            ->assertOk();
    }

    public function test_search_post_tag_returns_ok(): void
    {
        $author = $this->createAuthor();
        $this->createPost($author, [
            'title' => 'Tagged Searchable Post',
        ]);
        Tag::factory()->create(['title' => 'TaggedSearch']);

        $this->postJson('/api/search-post-tag', [
            'search' => 'Tagged',
        ])
            ->assertOk()
            ->assertJsonStructure(['posts', 'tags']);
    }

    public function test_active_pages_returns_ok(): void
    {
        $user = $this->createAuthor();

        Page::query()->create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => 'About content',
            'status' => 1,
            'user_id' => $user->id,
            'priority' => 1,
            'image' => null,
        ]);

        $this->getJson('/api/pages')
            ->assertOk();
    }

    public function test_single_page_returns_ok(): void
    {
        $user = $this->createAuthor();

        Page::query()->create([
            'title' => 'Contact',
            'slug' => 'contact',
            'content' => 'Contact content',
            'status' => 1,
            'user_id' => $user->id,
            'priority' => 1,
        ]);

        $this->getJson('/api/page/contact')
            ->assertOk()
            ->assertJsonPath('slug', 'contact')
            ->assertJsonPath('title', 'Contact');
    }

    public function test_advertise_form_validation_fails(): void
    {
        $this->postJson('/api/advertise-form', [])
            ->assertStatus(400)
            ->assertJsonPath('status', 0);
    }

    public function test_advertise_form_stores_submission(): void
    {
        $payload = [
            'first_name' => 'Sara',
            'last_name' => 'Ahmadi',
            'phone' => '09121234567',
            'content' => 'I want to advertise',
            'token' => 'test-recaptcha-token',
        ];

        $this->postJson('/api/advertise-form', $payload)
            ->assertOk()
            ->assertJsonPath('status', 1);

        $this->assertDatabaseHas('advertise_forms', [
            'first_name' => 'Sara',
            'last_name' => 'Ahmadi',
            'phone' => '09121234567',
            'content' => 'I want to advertise',
        ]);
    }

    public function test_post_comments_returns_ok(): void
    {
        $author = $this->createAuthor();
        $commenter = User::factory()->create([
            'status' => 1,
            'role_id' => 4,
            'password' => bcrypt('Password1!'),
        ]);
        $post = $this->createPost($author);

        Comment::factory()->create([
            'text' => 'Great post',
            'user_id' => $commenter->id,
            'parent_id' => 0,
            'status' => 1,
            'is_report' => 0,
            'commentable_id' => $post->id,
            'commentable_type' => Post::class,
        ]);

        $this->getJson('/api/comment/post/'.$post->id)
            ->assertOk();
    }
}

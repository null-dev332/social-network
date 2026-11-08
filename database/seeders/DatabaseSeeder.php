<?php

namespace Database\Seeders;

use App\Domain\Models\Comment;
use App\Domain\Models\Message;
use App\Domain\Models\Post;
use App\Domain\Models\PostLike;
use App\Domain\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->user('Admin', 'admin@example.com', 'admin', 'I run this place.');
        $manager = $this->user('Eva Horáková', 'manager@example.com', 'manager', 'Keeping an eye on posts and comments. Owner of a very lazy pug.');
        $jan = $this->user('Jan Novák', 'jan@example.com', 'user', 'CS student, usually in the library.');
        $petra = $this->user('Petra Svobodová', 'petra@example.com', 'user', 'Football, mountains and sometimes lectures.');
        $tomas = $this->user('Tomáš Dvořák', 'tomas@example.com', 'user', 'Taking photos of old streets. Will reply once I find my charger.');

        $jan->following()->attach([$petra->id, $tomas->id, $manager->id]);
        $petra->following()->attach([$jan->id, $tomas->id, $manager->id]);
        $tomas->following()->attach([$jan->id, $petra->id]);
        $manager->following()->attach([$petra->id]);

        // posts, the number is how many hours ago it was posted
        $this->post($admin, 'Welcome! You can change your password in Settings.', null, 170);
        $this->post($manager, 'If you see a post that breaks the rules, just message me.', null, 160);
        $books = $this->post($jan, 'Got these from the library for the semester. Wish me luck.', 'books.jpg', 140);
        $fjord = $this->post($petra, 'Still thinking about this view from the summer trip.', 'fjord.jpg', 120);
        $pug = $this->post($manager, 'Monday mood.', 'pug-blanket.jpg', 100);
        $canal = $this->post($tomas, 'Found a really nice town on the way back home.', 'canal-town.jpg', 90);
        $this->post($jan, 'Switched to an e-reader, my backpack is so much lighter now.', 'ebook.jpg', 80);
        $lake = $this->post($petra, 'Weekend at the lake, no signal at all and it was great.', 'lake-canoe.jpg', 70);
        $camera = $this->post($tomas, 'Bought an old film camera at a flea market. Let\'s see if it still works.', 'camera.jpg', 60);
        $this->post($manager, 'Fresh strawberries from my grandma\'s garden.', 'strawberries.jpg', 50);
        $lunch = $this->post($jan, 'The cafeteria lunch was actually good today.', null, 40);
        $this->post($petra, 'Breakfast before practice.', 'breakfast-bowl.jpg', 30);
        $street = $this->post($tomas, 'Walking around the old part of the city.', 'old-street.jpg', 26);
        $football = $this->post($petra, 'Who is coming to play football behind the school tomorrow?', null, 20);
        $hike = $this->post($petra, 'Hiking day with the team.', 'valley-hike.jpg', 14);
        $this->post($tomas, 'If anyone finds a charger in room B5, it is mine.', null, 9);
        $this->post($manager, 'He refuses to get up.', 'pug-bed.jpg', 6);
        $mountains = $this->post($tomas, 'First snow in the mountains.', 'snowy-mountains.jpg', 4);
        $this->post($jan, 'Study session before the exam.', 'laptop-notes.jpg', 2);
        $latte = $this->post($jan, 'Third coffee today, the exam is tomorrow.', 'latte.jpg', 1);
        $this->post($tomas, 'Sunset by the sea.', 'lighthouse.jpg', 0);

        $this->comment($books, $petra, 'That is a lot of reading.');
        $this->comment($books, $jan, 'Two of them are just for the photo.');
        $this->comment($fjord, $tomas, 'Wow, where is this?');
        $this->comment($fjord, $petra, 'Norway, I really want to go back.');
        $this->comment($pug, $jan, 'Same.');
        $this->comment($canal, $petra, 'So pretty!');
        $this->comment($camera, $jan, 'Show us the first photos.');
        $this->comment($lunch, $petra, 'We had dumplings again, so I am jealous.');
        $this->comment($street, $manager, 'Love the colors.');
        $this->comment($football, $jan, 'Maybe, if it does not rain.');
        $this->comment($football, $tomas, 'I am in.');
        $this->comment($hike, $jan, 'Looks amazing.');
        $this->comment($mountains, $petra, 'Can\'t wait for the ski trip.');
        $this->comment($latte, $petra, 'Good luck tomorrow!');
        $this->comment($latte, $tomas, 'You will be fine.');

        $this->like([$books, $canal, $lake, $hike, $mountains, $pug], $petra);
        $this->like([$fjord, $lake, $street, $hike, $football, $pug], $jan);
        $this->like([$books, $fjord, $latte, $hike, $football], $tomas);
        $this->like([$fjord, $pug, $canal, $mountains, $street], $manager);
        $this->like([$pug, $mountains], $admin);

        Message::create([
            'sender_id' => $jan->id,
            'receiver_id' => $petra->id,
            'content' => 'Can you send me the notes from the lecture?',
            'read_at' => now()->subMinutes(30),
        ]);

        Message::create([
            'sender_id' => $petra->id,
            'receiver_id' => $jan->id,
            'content' => 'Sure, I will send them tonight.',
            'read_at' => null,
        ]);

        Message::create([
            'sender_id' => $tomas->id,
            'receiver_id' => $jan->id,
            'content' => 'Did you see my charger anywhere?',
            'read_at' => now()->subHours(2),
        ]);
    }

    private function user(string $name, string $email, string $role, string $bio): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'bio' => $bio,
            'email_verified_at' => now(),
        ]);
    }

    // photos are in database/seeders/photos and get copied to storage
    private function post(User $user, string $content, ?string $photo, int $hoursAgo): Post
    {
        $image = null;

        if ($photo) {
            $image = 'posts/'.$photo;
            Storage::disk('public')->put($image, file_get_contents(__DIR__.'/photos/'.$photo));
        }

        $post = Post::create([
            'user_id' => $user->id,
            'content' => $content,
            'image' => $image,
        ]);

        $post->created_at = now()->subHours($hoursAgo);
        $post->save();

        return $post;
    }

    private function comment(Post $post, User $user, string $content): void
    {
        Comment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => $content,
        ]);
    }

    private function like(array $posts, User $user): void
    {
        foreach ($posts as $post) {
            PostLike::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);
        }
    }
}

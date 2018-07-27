<?php

use App\Category;
use App\Quiz;
use App\User;
use App\Video;
use Illuminate\Database\Seeder;

class QuizzesTableSeeder extends Seeder
{
    private $videos = [
        ['pZCxyOcvp5A', 75, 93],
        ['GA27aQZCQMk', 17, 31],
        ['LDxcfTm1QTc', 100, 115],
        ['SpbV_qF01Kw', 19, 33],
        ['nScV1qu-MZQ', 35, 50],
        ['NaT-swfmMQw', 45, 60],
        ['Q4FT7BKr8GE', 134, 149],
        ['YoDh_gHDvkk', 10, 25],
        ['qMdwFkO8xA0', 60, 75],
        ['nY31ZH6hAFI', 82, 97],
        ['PobrSpMwKk4', 63, 78],
        ['xL5spALs-eA', 40, 55],
        ['xMaE6toi4mk', 0, 8],
        ['BqDjMZKf-wg', 0, 15],
        ['fH26boHgfQw', 23, 38],
        ['CSav51fVlKU', 20, 35],
    ];

    private $quizzes = [
        ["John Lennon had the last number one single of the year in the US in 1980, Who had the first with \"Please Don't Go\"?", ['KC and the Sunshine Band', 'Hot Chocolate', 'Rose Royce'], 0],
        ["\"There's a brand new dance but I don't know its name\" is the first line from which David Bowie hit?", ['Fame', 'Sound and Vision', 'Fashion'], 2],
        ['Which British band featured on the 1980 Olivia Newton-John hit "Xanadu"?', ['OMD', 'ELO', 'The Shadows'], 1],
        ['Who was a "Woman In Love" in 1980?', ['Barbra Streisand', 'Dionne Warwick', 'Diana Ross'], 0],
        ["Endless Love by Luther Vandross dueting with Mariah Carey, but with whom did Diana Ross have a huge hit in 1981?", ['Michael Jackson', 'Lionel Richie', 'Jermaine Jackson'], 1],
        ['A cover of "Being With You" by Kali Uchis but which Motown artist had a 1981 hit with this song?', ['Smokey Robinson', 'Marv Johnson', 'Edwin Starr'], 0],
        ['Dancing With Tears in My Eyes by Ultravox who had a UK number one hit in 1981 with which song named after a city?', ['Rome', 'New York', 'Vienna'], 2],
        ['Who sang with Queen on the hit "Under Pressure"?', ['David Bowie', 'Bing Crosby', 'Montserrat Caballe'], 0],
        ['Who had a 1981 hit with "Arthur\'s Theme"?', ['Joe Dolce', 'Christopher Cross', 'Julio Iglesias'], 1],
        ['"The One That You Love" was a hit in the US in 1981 for which Australian soft rock band?', ['Savage Garden', 'Jet', 'Air Supply'], 2],
        ['Which hip hop group has a 1980s hit with "The Message"?', ['Grandmaster Flash & The Furious 5', 'A Tribe Called Quest', 'Beastie Boys'], 0],
        ['Joan Jett had a hit with "I Love Rock and Roll". What is the name of her band?', ['The Aces', 'The Blackhearts', 'The Warlocks'], 1],
        ['Which band had a hit with "Should I Stay or Should I Go" in 1982?', ['Thin Lizzy', 'Sex Pistols', 'The Clash'], 2],
        ['Which band had a 1982 hit with "Centerfold"?', ['The J Geils Band', 'The Dazz Band', 'Asia'], 0],
        ['Who dueted with Paul McCartney on "Ebony and Ivory"?', ['Michael Jackson', 'Stevie Wonder', 'John Lennon'], 1],
        ['Which musician had a huge hit with the "Chariots of Fire Theme"?', ['Giorgio Moroder', 'Mike Oldfield', 'Vangelis'], 2],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();
        $users = User::all();
        $videos = collect();
        foreach ($this->videos as $video) {
            $videos->push(Video::create([
                'youtube_id' => $video[0],
                'start_time' => $video[1],
                'end_time' => $video[2],
            ]));
        }

        foreach ($videos as $key => $video) {
            Quiz::create([
                'type' => Quiz::VIDEO_THEN_QUESTION,
                'video_id' => $videos->get($key)->id,
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'question' => $this->quizzes[$key][0],
                'answers' => json_encode($this->quizzes[$key][1]),
                'correct_index' => $this->quizzes[$key][2]
            ]);
        }
    }
}

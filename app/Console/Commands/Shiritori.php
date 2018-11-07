<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class Shiritori extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Shiritori';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // フラグの初期化
        DB::table('words')
            ->where('flag', '=', 1)
            ->update([
                'flag' => 0
            ]);

        // 最初の一文字を取得
        $row = DB::table('words')
                ->where('flag', '=', 0)
                ->inRandomOrder()
                ->limit(1)
                ->get();

        while (count($row) >= 1) {
            // ワードの表示
            $word = preg_replace("/\n/", '',  $row[0]->word);
            echo $word . '(' . $row[0]->word_rb . ')' . "\n";

            // 同名のフラグを1にする
            $count = DB::table('words')
                            ->where('flag', '=', 0)
                            ->where('word_rb', '=', $row[0]->word_rb)
                            ->update([
                            'flag' => 1
                            ]);
            
            
            $next_first_moji = preg_split("//u", $row[0]->word_rb, -1, PREG_SPLIT_NO_EMPTY)[
                                    count(preg_split("//u", $row[0]->word_rb, -1, PREG_SPLIT_NO_EMPTY)) - 1
                                ];
            if($next_first_moji == "ー") {
                //　伸ばしぼうならその前の文字にする
                $next_first_moji = preg_split("//u", $row[0]->word_rb, -1, PREG_SPLIT_NO_EMPTY)[
                    count(preg_split("//u", $row[0]->word_rb, -1, PREG_SPLIT_NO_EMPTY)) - 2
                ];
            }
            // 小文字を大文字にする
            $next_first_moji = $this->kanaSmallToLarge($next_first_moji);

            // 次のワードを取得
            $row = DB::table('words')
                ->where('flag', '=', 0)
                ->where('word_rb', 'like', "$next_first_moji%")
                ->where('word_rb', 'not like', "%ん")
                ->inRandomOrder()
                ->limit(1)
                ->get();
        }

        echo "参った！\n";
    }

    /**
     * 小文字を大文字にする
     */
    private function kanaSmallToLarge($subject) {
        $search = Array('ぁ','ぃ','ぅ','ぇ','ぉ','っ','ゃ','ゅ','ょ','ゎ','ァ','ィ','ゥ','ェ','ォ','ッ','ャ','ュ','ョ','ヮ');
        $replace = Array('あ','い','う','え','お','つ','や','ゆ','よ','わ','ア','イ','ウ','エ','オ','ツ','ヤ','ユ','ヨ','ワ');
        return str_replace($search, $replace, $subject);
    }
}

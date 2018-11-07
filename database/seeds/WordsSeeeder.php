<?php

use Illuminate\Database\Seeder;

class WordsSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fpr = fopen("./outData.csv", 'r');
        while ($line = fgets($fpr)) {
            $tmp = explode(',', $line);

            $word = preg_replace("/^\"/", '', $tmp[1]);
            $word = preg_replace("/\"$/", '',  $word);

            $word_rb = preg_replace("/^\"/", '', $tmp[0]);
            $word_rb = preg_replace("/\"$/", '',  $word_rb);
            
            DB::table('words')->insert(
                [
                    'word' => $word,
                    'word_rb' => $word_rb,
                    'flag' => 0
                ]
            );

        }
        fclose($fpr);
    }
}

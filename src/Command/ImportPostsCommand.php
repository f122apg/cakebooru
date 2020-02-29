<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Command;

use App\Utility\File;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class ImportPostsCommand extends Command
{
    const ALLOW_EXT = [
        'jpg',
        'png',
        'gif',
        'bmp',
    ];

    const COMMANDS = [
        'SOURCE_DIR' => 'source',
        'TAGS_CSV' => 'csv'
    ];

    public $tags = [];

    /**
     * initialize
     *
     * @return void
     */
    public function initialize() : void
    {
        parent::initialize();
        $this->loadModel('Posts');
        $this->loadModel('Tags');
    }

    /**
     * execute
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io) : int
    {
        $sourceDir = $args->getArgument(self::COMMANDS['SOURCE_DIR']);
        $tagsCsv = $args->getArgument(self::COMMANDS['TAGS_CSV']);
        if ($tagsCsv !== null) {
            $this->setTags($tagsCsv);
        }

        //サブディレクトリはとりあえず考慮しない
        $files = scandir($sourceDir);

        //トレイリングスラッシュを付ける
        if (substr($sourceDir, -1) !== '/' || substr($sourceDir, -1) !== '\\') {
            $sourceDir .= '/';
        }

        foreach ($files as $file) {
            //ディレクトリだったら次のループへ
            if ($file === '.' || $file === '..' || is_dir($file)) {
                continue;
            }

            $ext = File::getExt($file);
            if (array_search($ext, self::ALLOW_EXT) === false) {
                continue;
            }

            $io->out($file . ' adding...', 0);

            //entity生成
            $post = $this->Posts->newEntity([
                'file' => File::createUploadedFile($sourceDir . $file),
                'user_id' => 1,
                'tags' => $this->Tags->createBtmData($this->getTags($file))
            ]);

            if ($this->Posts->save($post)) {
                $io->overwrite($file . ' added!');
            }
        }

        return 0;
    }

    /**
     * 引数の受け取り
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to update
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument(self::COMMANDS['SOURCE_DIR'], [
            'help' => 'source images dir.',
            'required' => true
        ]);

        $parser->addArgument(self::COMMANDS['TAGS_CSV'], [
            'help' => 'tags csv.'
        ]);

        return $parser;
    }

    /**
     * タグをプロパティにセットする
     *
     * @param string $csvFile csvのファイルパス
     * @return void
     */
    public function setTags($csvFile): void
    {
        $csv = null;
        try {
            $csv = new \SplFileObject($csvFile);
        } catch(Exception $ex) {
            return;
        }

        if (!$csv->isReadable()) {
            return;
        }

        $csv->setFlags(\SplFileObject::READ_CSV);

        while(!$csv->eof()) {
            //SJIS to UTF-8
            $line = mb_convert_encoding($csv->fgetcsv(), 'UTF-8', 'SJIS');
            $fileName = '';

            foreach ($line as $k => $tag) {
                //1列目はファイル名とみなす
                if ($k === 0) {
                    $fileName = $tag;
                    $this->tags[$fileName] = [];
                } elseif (array_search($tag, $this->tags[$fileName]) === false) {
                    $this->tags[$fileName][] = [
                        'tag' => $tag
                    ];
                }
            }

            $csv->next();
        }
    }

    /**
     * 画像ファイルに紐づく、csv内のタグを取得する
     *
     * @param string $fileName ファイル名
     * @return array
     */
    public function getTags($fileName): array
    {
        return $this->tags[$fileName] ?? [];
    }
}

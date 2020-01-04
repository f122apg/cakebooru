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

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class ImportPostsCommand extends Command
{
    /**
     * initialize
     *
     * @return void
     */
    public function initialize() : void
    {
        parent::initialize();
        $this->loadModel('Posts');
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
        $sourceDir = $args->getArgument('sourceDir');
        $tagsCsv = $args->getArgument('tagsCsv');

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

            $io->out($file . ' adding...', 0);

            //前準備
            $post = $this->Posts->newEmptyEntity();
            $streamFactory = new \Zend\Diactoros\StreamFactory();
            $uploadedFileFactory = new \Zend\Diactoros\UploadedFileFactory();

            //ファイルをアップロードしたと見せかける
            $stream = $streamFactory->createStreamFromFile($sourceDir . $file);
            $post->file = $uploadedFileFactory->createUploadedFile($stream, null, UPLOAD_ERR_OK, $file);

            $post->user_id = 1;
            $post->tags = '';

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
        $parser->addArgument('sourceDir', [
            'help' => 'source images dir.'
        ]);

        $parser->addArgument('tagsCsv', [
            'help' => 'tags csv.'
        ]);

        return $parser;
    }
}

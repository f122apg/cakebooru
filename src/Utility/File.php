<?php
declare(strict_types=1);

namespace App\Utility;

use Cake\Core\Configure;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFile;
use Laminas\Diactoros\UploadedFileFactory;

/**
 * File
 */
class File
{
    /**
     * ディレクトリの存在チェック
     *
     * @param string $path ディレクトリパス
     * @return bool
     */
    public static function existsDir(string $path) : bool
    {
        return file_exists($path) && is_dir($path);
    }

    /**
     * ファイルの存在チェック
     *
     * @param string $filename ファイル名
     * @return bool
     */
    public static function existsFile(string $filename) : bool
    {
        return file_exists(self::getImagePath('UploadedImages', $filename));
    }

    /**
     * 画像が格納されているパスを取得する
     *
     * @param string $needPath 取得したいパス。指定されなければ、arrayを返す
     * @param string $filename ファイル名。指定された場合、ファイル名まで指定されたパスを取得する
     * @return array|string $needPathが指定されればそのパスを返し、指定がなければ、arrayを返す
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function getImagePath(string $needPath = null, string $filename = '')
    {
        $paths = Configure::read('ImagesPaths');
        foreach ($paths as $k => $path) {
            //相対パスが指定されることも考慮して、cakebooruのルートから絶対パスを作成
            $path = dirname(dirname(__DIR__)) . DS . $path;

            //存在チェック
            if (self::existsDir($path)) {
                $fullPaths[$k] = $path;
                //加工前のパスで存在チェック
            } elseif (self::existsDir($paths[$k])) {
                $fullPaths[$k] = $paths[$k];
            } else {
                //加工前のパスを表示する
                throw new \RuntimeException('Not found path. path:' . $paths[$k]);
            }

            //トレイリングスラッシュをつけておく
            $fullPaths[$k] = $fullPaths[$k] . DS . $filename;
        }

        if (!isset($fullPaths[$needPath])) {
            throw new \InvalidArgumentException('Not found path. needPath:' . $needPath);
        }

        return $needPath !== null
            ? $fullPaths[$needPath]
            : $fullPaths;
    }

    /**
     * ファイル名から拡張子を取得する
     *
     * @param string $filename ファイル名 パスでも可
     * @return string 拡張子
     */
    public static function getExt(string $filename) : string
    {
        $currentLocale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $fileInfo = pathinfo($filename);
        setlocale(LC_ALL, $currentLocale);

        return $fileInfo['extension'];
    }
    /**
     * アップロードされたファイルの中身を取得
     *
     * @param string $filename ファイル名
     * @return string
     */
    public static function getUploadedFileContent(string $filename) : string
    {
        return file_get_contents(self::getImagePath('UploadedImages', $filename));
    }

    /**
     * サムネイルの中身を取得
     *
     * @param string $filename ファイル名
     * @return string
     */
    public static function getThumbnailFileContent(string $filename) : string
    {
        return file_get_contents(self::getImagePath('ThumbnailImages', $filename));
    }

    /**
     * 疑似的にアップロードされたファイルを作成する
     *
     * @param string $filePath ファイルパス
     * @return UploadedFile
     */
    public static function createUploadedFile(string $filePath) : UploadedFile
    {
        $streamFactory = new StreamFactory();
        $uploadedFileFactory = new UploadedFileFactory();

        //ファイルをアップロードしたと見せかける
        $stream = $streamFactory->createStreamFromFile($filePath);

        $currentLocale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $filename = basename($filePath);
        setlocale(LC_ALL, $currentLocale);

        return $uploadedFileFactory->createUploadedFile($stream, null, UPLOAD_ERR_OK, $filename);
    }
}
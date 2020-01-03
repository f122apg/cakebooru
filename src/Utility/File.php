<?php
declare(strict_types=1);

namespace App\Utility;

use Cake\Core\Configure;

/**
 * File
 */
class File
{
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
     * ファイルの存在チェック
     *
     * @param string $filename ファイル名
     * @return bool
     */
    public static function existsFile(string $filename) : bool
    {
        return file_exists(Configure::read('UploadedImageDest') . '/' . $filename);
    }

    /**
     * アップロードされたファイルの中身を取得
     *
     * @param string $filename ファイル名
     * @return string
     */
    public static function getUploadedFileContent(string $filename) : string
    {
        return file_get_contents(Configure::read('UploadedImageDest') . '/' . $filename);
    }

    /**
     * サムネイルの中身を取得
     *
     * @param string $filename ファイル名
     * @return string
     */
    public static function getThumbnailFileContent(string $filename) : string
    {
        return file_get_contents(Configure::read('ThumbnailImageDest') . '/' . $filename);
    }
}
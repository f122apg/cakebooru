<?php
declare(strict_types=1);

namespace App\Utility;

use App\Utility\File;
use Cake\Core\Configure;

/**
 * Image
 */
class Image
{
/**
 * ハッシュアルゴリズム
 *
 * @var string
 */
    public const HASH_ALGO = 'sha512';

    /**
     * base64でエンコードされた後のファイル名を取得する
     *
     * @param string $path 画像のパス
     * @param string $binary バイナリ
     * @return string
     */
    public static function getEncodedFilename(string $path, string $binary = null) : string
    {
        $base64Image = base64_encode($binary ?? file_get_contents($path));
        $ext = File::getExt($path);
        $filename = hash(self::HASH_ALGO, $base64Image) . '.' . $ext;

        return $filename;
    }

    /**
     * base64で画像をエンコードする
     *
     * @param string $path 画像のパス
     * @param string $dest 出力先 nullの場合、app.phpのUploadedImagesに設定されているパスに出力する
     * @return string
     */
    public static function encodeImage(string $path, string $dest = null) : string
    {
        $filename = self::getEncodedFilename($path);
        $image = file_get_contents($path);
        $base64Image = base64_encode($image);

        $fp = fopen($dest ?? File::getImagePath('UploadedImages', $filename), 'w');
        fwrite($fp, $base64Image);
        fclose($fp);

        return $filename;
    }

    /**
     * 画像をbase64でデコードする
     *
     * @param string $path 画像のパス
     * @return string
     */
    public static function decodeImage(string $path) : string
    {
        $image = file_get_contents($path);
        return base64_decode($image);
    }

    /**
     * サムネイルを出力する
     * reference:https://qiita.com/suin/items/b01eebc05209dba0eb3e
     *
     * @param string $path 画像のパス
     * @param string $dest 出力先 nullの場合、app.phpのThumbnailImagesに設定されているパスに出力する
     * @return string サムネイルのファイル名
     */
    public static function createThumbnail(string $path, string $dest = null, int $width = null, int $height = null) : string
    {
        if (is_null($dest)) {
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $dest = File::getImagePath('ThumbnailImages', $filename) . '.jpg';
        }

        list($originalWidth, $originalHeight) = self::getImageSize($path);
        $thumbnailSize = Configure::read('ThumbnailSize');
        list($canvasWidth, $canvasHeight) = self::getContainSize($originalWidth, $originalHeight, $width ?? $thumbnailSize['width'], $height ?? $thumbnailSize['height']);
        self::saveResizeImage($path, $dest, $canvasWidth, $canvasHeight);

        return basename($dest);
    }

    /**
     * 画像のサイズを変形して保存する
     * reference:https://qiita.com/suin/items/b01eebc05209dba0eb3e
     *
     * @param string $path 元画像のファイルパス
     * @param string $destPath 出力先のパス
     * @param int $width 横幅
     * @param int $height 縦幅
     * @return void
     */
    public static function saveResizeImage($path, $destPath, $width, $height) : void
    {
        list($originalWidth, $originalHeight, $type) = self::getImageSize($path);
        //base64の画像を通常の画像に戻す
        $tmp = tmpfile();
        fwrite($tmp, self::decodeImage($path));
        $path = stream_get_meta_data($tmp)['uri'];

        switch ($type) {
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($path);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($path);
                break;
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($path);
                break;
            case IMAGETYPE_BMP:
                $source = imagecreatefrombmp($path);
                break;
            default:
                throw new \Exception('not support image:' . $type);
        }

        fclose($tmp);

        $canvas = imagecreatetruecolor($width, $height);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
        imagejpeg($canvas, $destPath, 90);
        imagedestroy($source);
        imagedestroy($canvas);

        //base64で保存
        self::encodeImage($destPath, $destPath);
    }

    /**
     * 内接サイズを計算する
     * reference:https://qiita.com/suin/items/b01eebc05209dba0eb3e
     *
     * @param int $width オリジナルの横幅
     * @param int $height オリジナルの縦幅
     * @param int $containerWidth 希望する横幅
     * @param int $containerHeight 希望する縦幅
     * @return array
     */
    public static function getContainSize($width, $height, $containerWidth, $containerHeight) : array
    {
        $ratio = $width / $height;
        $containerRatio = $containerWidth / $containerHeight;
        if ($ratio > $containerRatio) {
            return [$containerWidth, intval($containerWidth / $ratio)];
        } else {
            return [intval($containerHeight * $ratio), $containerHeight];
        }
    }

    /**
     * getimagesizeのbase64対応版
     *
     * @param string $path 画像のファイルパス
     * @return array
     */
    public static function getImageSize($path) : array
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($path);

        if ($mimeType === 'text/plain') {
            $tmp = tmpfile();
            fwrite($tmp, self::decodeImage($path));

            $path = stream_get_meta_data($tmp)['uri'];
        }

        $imageInfo = getimagesize($path);

        if (isset($tmp)) {
            fclose($tmp);
        }

        return $imageInfo;
    }
}

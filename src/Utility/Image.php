<?php
declare(strict_types=1);

namespace App\Utility;

use Cake\Core\Configure;
use App\Utility\File;

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
     * @param string $dest 出力先 nullの場合、app.phpのUploadedImageDestに設定されているパスに出力する
     * @return string
     */
    public static function encodeImage(string $path, string $dest = null) : string
    {
        $filename = self::getEncodedFilename($path);
        $image = file_get_contents($path);
        $base64Image = base64_encode($image);

        $fp = fopen($dest ?? Configure::read('UploadedImageDest') . '/' . $filename, 'w');
        fwrite($fp, $base64Image);
        fclose($fp);

        return $filename;
    }
}

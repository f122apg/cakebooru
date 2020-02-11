<?php
declare(strict_types=1);
namespace App\Model\Validation;

use Cake\Validation\Validation;

class FileValidation extends Validation
{
/**
 * 受け取れる拡張子
 *
 * @var array
 */
    public array $acceptExts = [
        'jpg',
        'jpeg',
        'png',
        'bmp',
        'gif',
    ];

    /**
     * システムで受け取れる拡張子かチェック
     *
     * @param \Laminas\Diactoros\UploadedFile $check アップロードされたファイル
     * @return bool
     */
    public function isAcceptExt(\Laminas\Diactoros\UploadedFile $check) : bool
    {
        $currentLocale = setlocale(LC_ALL, 0);
        //multibyte support
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $fileInfo = pathinfo($check->getClientFilename());
        setlocale(LC_ALL, $currentLocale);

        foreach ($this->acceptExts as $ext) {
            if ($fileInfo['extension'] === $ext) {
                return true;
            }
        }

        return false;
    }
}
?>
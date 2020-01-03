<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Core\Configure;
use App\Utility\Image;
use App\Utility\File;

/**
 * Post Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $filename
 * @property string $ext
 * @property string $tags
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Favorite[] $favorites
 */
class Post extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'filename' => true,
        'file' => true, //アップロードされたファイルがリクエストデータに入るのをキャッチするため
        'ext' => true,
        'tags' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'favorites' => true,
    ];

    /**
     * アップロードされたファイルからファイル名を取得する
     *
     * @return string
     */
    public function getFilenameByFile() : string
    {
        return Image::getEncodedFilename($this->file->getClientFilename(), $this->file->getStream()->getContents());
    }

    /**
     * アップロードされたファイルからファイル名を取得する
     *
     * @return string ファイル名
     * @throws Exception\UploadedFileAlreadyMovedException if already moved.
     * @throws Exception\UploadedFileErrorException if the upload was not successful.
     * @throws Exception\InvalidArgumentException if the $path specified is invalid.
     * @throws Exception\UploadedFileErrorException on any error during the
     *     move operation, or on the second or subsequent call to the method.
     */
    public function uploadFile() : string
    {
        $filename = $this->getFilenameByFile();
        $exists = File::existsFile($filename);
        if ($exists) {
            throw new \Exception('Exists file. Cannot upload.');
        }

        //アップロード（base64にするため、tmpファイルへ書き込み）
        $tmp = tmpfile();
        //tmpファイルのパスを取得
        $meta = stream_get_meta_data($tmp);
        //tmpファイルを.jpg等にrename（これをしないと、encodeImageしたときに.tmpで保存されてしまうため）
        $tmpInfo = pathinfo($meta["uri"]);
        $tmpPath = $tmpInfo['dirname'] . '/' . $tmpInfo['filename'] . '.' . File::getExt($filename);
        rename($meta["uri"], $tmpPath);
        //tmpファイルへアップロードされたファイルを書き込み
        $this->file->moveTo($tmpPath);
        //tmpファイルへ書き込まれた画像ファイルをbase64にして保存しなおす
        Image::encodeImage($tmpPath);
        fclose($tmp);

        return $filename;
    }

    /**
     * インサートの準備
     *
     * @return void
     */
    public function prepareInsert() : void
    {
        $this->filename = $this->uploadFile();
        $this->ext = File::getExt($this->filename);
    }
}

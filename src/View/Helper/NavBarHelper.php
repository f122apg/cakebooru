<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\View;
use Cake\View\Helper;
use Cake\Routing\Router;

class NavBarHelper extends Helper
{
    public $helpers = ['Html'];

    /**
     * navbarのリンクアイテムを出力
     *
     * @param string $name 名前
     * @param string $href リンク先
     * @param array $options Html->linkのオプション
     * @return string
     */
    public function link($name, $href, $options = []) : string
    {
        $liTagClass = 'nav-item';

        //現在アクセスしているurlと設定されているリンク先が合っていれば
        //今そのnavbarがアクセスしているとみなし、activeを追加する
        if (strpos(Router::url(), $href) !== false) {
            $liTagClass .= ' active';
        }

        $html = '<li class="' . $liTagClass . '">';
        $html .= $this->Html->link(
            __d('cakebooru', $name),
            $href,
            array_merge(
                ['class' => 'nav-link'],
                $options
            )
        );
        $html .= '</li>';

        return $html;
    }
}
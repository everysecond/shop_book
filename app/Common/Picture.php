<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/14
 * Time: 10:46
 */

namespace App\Common;


class Picture {
    protected $picture;
    protected $placeholder;

    public function __construct($picture = null) {
        $this->picture = $picture;
        $this->placeholder = '';
    }

    public function __toString() {
        if (is_null($this->picture)) {
            return $this->placeholder;
        }
        return $this->picture;
    }

    public function crop($width = 200, $height = 200) {
        if (is_null($this->picture)) {
            return $this->placeholder;
        }
        //$pos = strrpos($this->picture, '.');
        return $this->picture . "?x-oss-process=style/{$width}X{$height}";
        //return substr($this->picture, 0, $pos) . '_' . $width . 'x' . $height . substr($this->picture, $pos);
    }
}
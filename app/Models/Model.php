<?php
/**
 * Created by PhpStorm.
 * User: Madman
 * Date: 2019/7/10
 * Time: 10:48
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

use ay\lib\Db;

class Model extends Db
{
    public function __construct()
    {
        $option = C();
        parent::__construct($option);
    }
}

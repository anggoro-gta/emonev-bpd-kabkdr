<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsSKPD extends Model
{
    protected $guard = [];
    protected $table = 'ms_skpd';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
}

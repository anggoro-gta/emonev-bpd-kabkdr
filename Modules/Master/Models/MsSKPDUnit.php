<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsSKPDUnit extends Model
{
    protected $guard = [];
    protected $table = 'ms_skpd_unit';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
}

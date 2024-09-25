<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;

class VRProgram extends Model
{
    protected $guarded = [''];
    protected $table = 'v_realisasi_program';
    public $incrementing = false;
    public $timestamps = false;
}

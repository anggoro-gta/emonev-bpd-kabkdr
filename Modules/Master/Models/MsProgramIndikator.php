<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class MsProgramIndikator extends Model
{
    protected $guarded = [''];
    protected $table = 'ms_program_indikator';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
}

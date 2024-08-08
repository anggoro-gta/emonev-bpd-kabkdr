<?php

namespace Modules\Realisasi\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $guarded = [''];
    protected $table = 't_realisasi_program';
    protected $primaryKey  = 'id';
    public $incrementing = false;
    public $timestamps = false;
}

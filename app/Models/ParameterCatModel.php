<?php

namespace App\Models;

use CodeIgniter\Model;

class ParameterCatModel extends Model
{
    protected $table = 'parameter_cat';
    protected $primaryKey = 'parameter_id';
    protected $allowedFields = [
        'jenis_ujian_id', 'se_target', 'delta_se_minimum',
      
        'jumlah_soal_maksimum', 'theta_awal'
    ];
}
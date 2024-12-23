<?php

namespace App\Models;

use CodeIgniter\Model;

class CatEstimationModel extends Model
{
    protected $table = 'cat_estimation';
    protected $primaryKey = 'estimation_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'peserta_ujian_id',
        'theta',
        'standard_error',
        'previous_se',
        'jumlah_soal'
    ];

    protected $useTimestamps = false;
}
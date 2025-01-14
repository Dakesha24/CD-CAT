<?php
namespace App\Models;

use CodeIgniter\Model;

class SoalUjianModel extends Model
{
    protected $table = 'soal_ujian';
    protected $primaryKey = 'soal_id';
    protected $allowedFields = [
        'ujian_id', 'pertanyaan', 'pilihan_a', 'pilihan_b',
        'pilihan_c', 'pilihan_d', 'jawaban_benar', 'tingkat_kesulitan'
    ];

    protected $useTimestamps = false;
}
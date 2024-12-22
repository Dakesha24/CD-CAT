<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailJawabanModel extends Model
{
    protected $table = 'detail_jawaban';
    protected $primaryKey = 'jawaban_id';
    protected $allowedFields = [
        'peserta_ujian_id',
        'soal_id',
        'jawaban_siswa',
        'is_correct',
        'waktu_menjawab'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'waktu_menjawab';
    protected $updatedField  = '';

    // Tambahkan validasi
    protected $validationRules = [
        'peserta_ujian_id' => 'required|numeric',
        'soal_id'         => 'required|numeric',
        'jawaban_siswa'   => 'required',
        'is_correct'      => 'required'
    ];
}
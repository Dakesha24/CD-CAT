<?php

namespace App\Models;

use CodeIgniter\Model;

class BankSoalModel extends Model
{
    protected $table = 'bank_soal';
    protected $primaryKey = 'soal_id';
    protected $allowedFields = [
        'guru_id',
        'jenis_ujian_id',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'jawaban_benar',
        'tingkat_kesulitan'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Add this method to get soal with jenis ujian data
    public function getSoalWithJenisUjian()
    {
        return $this->select('bank_soal.*, jenis_ujian.nama_ujian')
                    ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_soal.jenis_ujian_id')
                    ->findAll();
    }

    // Method to get single soal with complete data
    public function getSoalById($soalId)
    {
        return $this->select('bank_soal.*, jenis_ujian.nama_ujian')
                    ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_soal.jenis_ujian_id')
                    ->where('bank_soal.soal_id', $soalId)
                    ->first();
    }

    // Method to get soal by guru
    public function getSoalByGuru($guruId)
    {
        return $this->select('bank_soal.*, jenis_ujian.nama_ujian')
                    ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = bank_soal.jenis_ujian_id')
                    ->where('bank_soal.guru_id', $guruId)
                    ->findAll();
    }
}
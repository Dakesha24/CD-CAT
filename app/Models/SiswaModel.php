<?php
namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'siswa_id';
    protected $allowedFields = ['user_id', 'kelas_id', 'nomor_peserta', 'nama_lengkap'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Fungsi untuk mengecek apakah data siswa sudah ada
    public function checkSiswaExists($userId)
    {
        $result = $this->where('user_id', $userId)->first();
        return !empty($result);
    }
}
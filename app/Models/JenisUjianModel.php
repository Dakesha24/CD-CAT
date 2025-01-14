<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisUjianModel extends Model
{
    protected $table = 'jenis_ujian';
    protected $primaryKey = 'jenis_ujian_id';
    protected $allowedFields = ['nama_jenis', 'deskripsi'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
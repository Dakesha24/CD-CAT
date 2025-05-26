<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianModel extends Model
{
  protected $table = 'ujian';
  protected $primaryKey = 'id_ujian';
  protected $allowedFields = ['jenis_ujian_id', 'nama_ujian', 'deskripsi', 'se_awal', 'se_minimum', 'delta_se_minimum', 'durasi'];
  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
}

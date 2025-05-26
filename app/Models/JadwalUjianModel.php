<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalUjianModel extends Model
{
  protected $table = 'jadwal_ujian';
  protected $primaryKey = 'jadwal_id';
  protected $allowedFields = ['ujian_id', 'kelas_id', 'guru_id', 'tanggal_mulai', 'tanggal_selesai', 'kode_akses', 'status'];
  protected $useTimestamps = false;

  public function getJadwalWithRelations()
  {
    return $this->select('jadwal_ujian.*, ujian.nama_ujian, kelas.nama_kelas, guru.nama_lengkap')
      ->join('ujian', 'ujian.id_ujian = jadwal_ujian.ujian_id')
      ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
      ->join('guru', 'guru.guru_id = jadwal_ujian.guru_id')
      ->findAll();
  }

  public function getJadwalUjianSiswa($kelasId)
  {
    return $this->db->table('jadwal_ujian ju')
      ->select('ju.*, u.nama_ujian, u.deskripsi, u.durasi,
                     k.nama_kelas')
      ->join('ujian u', 'u.id_ujian = ju.ujian_id')
      ->join('kelas k', 'k.kelas_id = ju.kelas_id')
      ->where('ju.kelas_id', $kelasId)
      ->where('ju.tanggal_selesai >=', date('Y-m-d H:i:s'))
      ->orderBy('ju.tanggal_mulai', 'ASC')
      ->get()
      ->getResultArray();
  }

  
}

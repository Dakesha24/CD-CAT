<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalUjianModel extends Model
{
  protected $table = 'jadwal_ujian';
  protected $primaryKey = 'jadwal_id';
  protected $allowedFields = [
    'jenis_ujian_id',
    'kelas_id',
    'guru_id',
    'tanggal_mulai',
    'tanggal_selesai',
    'durasi_menit',
    'kode_akses',
    'status'
  ];

  public function getAvailableUjianWithStatus($siswaId)
  {
    return $this->select('jadwal_ujian.*, jenis_ujian.nama_ujian, 
                         peserta_ujian.status as peserta_status,
                         peserta_ujian.peserta_ujian_id')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->join('siswa', 'siswa.kelas_id = jadwal_ujian.kelas_id')
      ->join('peserta_ujian', 'peserta_ujian.jadwal_id = jadwal_ujian.jadwal_id 
                          AND peserta_ujian.siswa_id = siswa.siswa_id', 'left')
      ->where('siswa.siswa_id', $siswaId)
      ->where('jadwal_ujian.tanggal_selesai >=', date('Y-m-d H:i:s'))
      ->findAll();
  }

  public function getAvailableUjian($userId)
  {
    return $this->select('jadwal_ujian.*, jenis_ujian.nama_ujian')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->join('siswa', 'siswa.kelas_id = jadwal_ujian.kelas_id')
      ->where('siswa.user_id', $userId)
      ->where('jadwal_ujian.tanggal_selesai >=', date('Y-m-d H:i:s'))
      ->findAll();
  }
}

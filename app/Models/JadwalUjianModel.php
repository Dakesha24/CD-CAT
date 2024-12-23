<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;
use DateTimeZone;

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

  public function getJadwalUjianByGuru($guruId)
  {
    return $this->select('jadwal_ujian.*, jenis_ujian.nama_ujian, kelas.nama_kelas')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
      ->where('jadwal_ujian.guru_id', $guruId) // Pastikan kolom 'guru_id' berasal dari tabel jadwal_ujian
      ->findAll();
  }

  public function getUjianToday($guruId)
  {
    $startOfDay = (new DateTime('today', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d 00:00:00');
    $endOfDay = (new DateTime('today', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d 23:59:59');

    return $this->where('guru_id', $guruId)
      ->groupStart()
      ->where('tanggal_mulai <=', $endOfDay)
      ->where('tanggal_selesai >=', $startOfDay)
      ->groupEnd()
      ->countAllResults();
  }

  public function getUpcomingUjian($guruId)
  {
    return $this->select('jadwal_ujian.*, jenis_ujian.nama_ujian, kelas.nama_kelas')
      ->join('jenis_ujian', 'jenis_ujian.jenis_ujian_id = jadwal_ujian.jenis_ujian_id')
      ->join('kelas', 'kelas.kelas_id = jadwal_ujian.kelas_id')
      ->where('guru_id', $guruId)
      ->where('tanggal_mulai >', date('Y-m-d H:i:s'))
      ->findAll();
  }
}

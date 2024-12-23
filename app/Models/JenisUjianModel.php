<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisUjianModel extends Model
{
    protected $table = 'jenis_ujian';
    protected $primaryKey = 'jenis_ujian_id';
    protected $allowedFields = [
        'nama_ujian',
        'deskripsi'
    ];

    // Mendapatkan semua jenis ujian
    public function getAllJenisUjian()
    {
        return $this->findAll();
    }

    // Mendapatkan jenis ujian berdasarkan ID
    public function getJenisUjianById($id)
    {
        return $this->find($id);
    }

    // Mendapatkan jenis ujian dengan jumlah soal
    public function getJenisUjianWithSoalCount()
    {
        return $this->select('jenis_ujian.*, COUNT(bank_soal.soal_id) as jumlah_soal')
                    ->join('bank_soal', 'bank_soal.jenis_ujian_id = jenis_ujian.jenis_ujian_id', 'left')
                    ->groupBy('jenis_ujian.jenis_ujian_id')
                    ->findAll();
    }

    // Mendapatkan jenis ujian yang aktif (memiliki jadwal)
    public function getActiveJenisUjian()
    {
        return $this->select('jenis_ujian.*, COUNT(DISTINCT jadwal_ujian.jadwal_id) as jumlah_jadwal')
                    ->join('jadwal_ujian', 'jadwal_ujian.jenis_ujian_id = jenis_ujian.jenis_ujian_id', 'left')
                    ->where('jadwal_ujian.status !=', 'selesai')
                    ->orWhere('jadwal_ujian.status IS NULL')
                    ->groupBy('jenis_ujian.jenis_ujian_id')
                    ->findAll();
    }

    // Validasi apakah jenis ujian sudah digunakan
    public function isUjianUsed($jenisUjianId)
    {
        $result = $this->db->table('bank_soal')
                          ->where('jenis_ujian_id', $jenisUjianId)
                          ->countAllResults();
        
        return $result > 0;
    }

    // Mencari jenis ujian berdasarkan nama
    public function searchByNama($keyword)
    {
        return $this->like('nama_ujian', $keyword)->findAll();
    }

    // Mendapatkan statistik penggunaan jenis ujian
    public function getUjianStatistics($jenisUjianId)
    {
        $stats = [
            'total_soal' => $this->db->table('bank_soal')
                                    ->where('jenis_ujian_id', $jenisUjianId)
                                    ->countAllResults(),
            'total_jadwal' => $this->db->table('jadwal_ujian')
                                      ->where('jenis_ujian_id', $jenisUjianId)
                                      ->countAllResults(),
            'jadwal_aktif' => $this->db->table('jadwal_ujian')
                                      ->where('jenis_ujian_id', $jenisUjianId)
                                      ->where('status', 'sedang_berlangsung')
                                      ->countAllResults()
        ];
        
        return $stats;
    }

    // Insert jenis ujian baru dengan validasi
    public function insertJenisUjian($data)
    {
        // Cek apakah nama ujian sudah ada
        $existing = $this->where('nama_ujian', $data['nama_ujian'])->first();
        
        if ($existing) {
            return false; // Nama ujian sudah ada
        }
        
        return $this->insert($data);
    }

    // Update jenis ujian dengan validasi
    public function updateJenisUjian($id, $data)
    {
        // Cek apakah nama ujian sudah ada (kecuali untuk ID yang sedang diupdate)
        $existing = $this->where('nama_ujian', $data['nama_ujian'])
                        ->where('jenis_ujian_id !=', $id)
                        ->first();
        
        if ($existing) {
            return false; // Nama ujian sudah ada
        }
        
        return $this->update($id, $data);
    }
}
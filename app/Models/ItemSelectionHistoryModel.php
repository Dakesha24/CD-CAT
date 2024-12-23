<?php
namespace App\Models;

use CodeIgniter\Model;

class ItemSelectionHistoryModel extends Model
{
    protected $table = 'item_selection_history';
    protected $primaryKey = 'selection_id';
    protected $allowedFields = ['peserta_ujian_id', 'soal_id', 'theta_before', 'information_value'];
}
<?php
// app/Models/FeedbackModel.php
namespace App\Models;

use CodeIgniter\Model;

class FeedbackModel extends Model
{
  protected $table = 'feedbacks';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $returnType = 'array';
  protected $allowedFields = ['name', 'email', 'message', 'status'];
  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = '';

  protected $validationRules = [
    'name' => 'required|min_length[3]|max_length[100]',
    'email' => 'required|valid_email',
    'message' => 'required|min_length[10]'
  ];

  public function getUnreadCount()
  {
    return $this->where('status', 'unread')->countAllResults();
  }
  public function getPaginatedFeedback($limit = 5)
  {
    return $this->orderBy('created_at', 'DESC')
      ->paginate($limit);
  }
}

<?php
// app/Libraries/CATLibrary.php
namespace App\Libraries;

class CATLibrary
{
    protected $maxItems = 30;           // Maksimum soal
    protected $targetSE = 0.3;          // Target Standard Error
    protected $minDeltaSE = 0.01;       // Minimum perubahan SE
    protected $initialTheta = 0.0;      // Kemampuan awal
    
    // Hitung probabilitas menjawab benar
    public function calculateProbability($theta, $a, $b, $c) {
        $exp = exp($a * ($theta - $b));
        return $c + ((1 - $c) * ($exp / (1 + $exp)));
    }
    
    // Hitung fungsi informasi item
    public function calculateInformation($theta, $a, $b, $c) {
        $p = $this->calculateProbability($theta, $a, $b, $c);
        $q = 1 - $p;
        return pow($a, 2) * pow($q / $p, 2) * $p;
    }
    
    // Estimasi theta baru setelah jawaban
    public function estimateNewTheta($responses, $items) {
        // Implementasi Maximum Likelihood Estimation
        $theta = 0;
        $prevTheta = -999;
        
        while (abs($theta - $prevTheta) > 0.001) {
            $prevTheta = $theta;
            $numerator = 0;
            $denominator = 0;
            
            foreach ($responses as $idx => $resp) {
                $item = $items[$idx];
                $p = $this->calculateProbability($theta, $item['discrimination'], 
                                               $item['difficulty'], $item['guessing']);
                $q = 1 - $p;
                
                $numerator += $item['discrimination'] * ($resp - $p);
                $denominator += pow($item['discrimination'], 2) * $p * $q;
            }
            
            if ($denominator != 0) {
                $theta = $prevTheta + ($numerator / $denominator);
            }
        }
        
        return $theta;
    }
    
    // Pilih soal berikutnya
    public function selectNextItem($theta, $answeredItems, $availableItems) {
        $maxInfo = -1;
        $selectedItem = null;
        
        foreach ($availableItems as $item) {
            if (in_array($item['soal_id'], $answeredItems)) continue;
            
            $info = $this->calculateInformation($theta, $item['discrimination'], 
                                              $item['difficulty'], $item['guessing']);
            
            if ($info > $maxInfo) {
                $maxInfo = $info;
                $selectedItem = $item;
            }
        }
        
        return $selectedItem;
    }

    // Hitung SE
    public function calculateSE($theta, $items, $responses) {
        $information = 0;
        foreach ($items as $idx => $item) {
            $info = $this->calculateInformation($theta, $item['discrimination'], 
                                              $item['difficulty'], $item['guessing']);
            $information += $info;
        }
        return $information > 0 ? 1 / sqrt($information) : 999;
    }

    // Cek kriteria pemberhentian
    public function checkStoppingCriteria($currentSE, $previousSE, $numItems) {
        return ($currentSE <= $this->targetSE) ||
               (abs($previousSE - $currentSE) <= $this->minDeltaSE) ||
               ($numItems >= $this->maxItems);
    }
}
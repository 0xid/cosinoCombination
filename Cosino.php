<?php

class Cosino {

    public $fieldsCount;
    public $chipCount;
    private $firstCombination = array();
    private $combination = array();
    private $countCombination = 0;
    public $bufferLimit = 5;
    private $file = false;

    public function __construct($fieldsCount = 0, $chipCount = 0) {
        $this->fieldsCount = (int) $fieldsCount;
        $this->chipCount = (int) $chipCount;
        $this->_checkedFields();
        $this->_setFirstCombination();
        $this->_setCountCombination();
    }

    private function _checkedFields() {
        if ($this->fieldsCount <= 0) {
            throw new Exception("fieldsCount должен быть больше нуля.");
        } elseif ($this->chipCount <= 0) {
            throw new Exception("fieldsCount должен быть больше нуля.");
        } elseif ($this->fieldsCount < $this->chipCount) {
            throw new Exception("chipCount не может быть меньше fieldsCount.");
        }
    }

    private function _setFirstCombination() {
        for ($i = 0; $i < $this->chipCount; $i++) {
            $this->firstCombination[] = 1;
        }
        for ($i = 0; $i < $this->fieldsCount - $this->chipCount; $i++) {
            $this->firstCombination[] = 0;
        }
    }

    private function _setCountCombination() {
        $n = $this->fieldsCount;
        $k = $this->chipCount;
        $t = 1;

        for ($i = $n, $c = $n - ($k - 1); $i >= $c; $i--)
            $t *= $i;
        for ($i = $k; $i >= 1; $i--)
            $t /= $i;

        $this->countCombination = $t;
    }

    private function _shiftLastOne() {
        $iOne = $this->_searchRightOne($this->combination['last']);
        $this->combination['last'][$iOne] = 0;
        $this->combination['last'][$iOne + 1] = 1;
        return $iOne + 1;
    }

    private function _AddOneAfterLastOne() {
        $lastCount = count($this->combination['last']) - 1;
        unset($this->combination['last'][$lastCount]);
        $lastCount--;
        if ($this->combination['last'][$lastCount] == 1) {
            $this->_AddOneAfterLastOne();
        } else {
            $iOne = $this->_shiftLastOne();
        }
        $iOne = $this->_searchRightOne($this->combination['last']);
        if ($lastCount == $iOne) {
            $this->combination['last'][$iOne + 1] = 1;
        } else {
            array_splice($this->combination['last'], $iOne, 0, array(1));
        }
    }

    private function _saveInFile() {
        $str = '';
        foreach ($this->combination['buffer'] as $buffer) {
            if(is_array($buffer)){
                $str .= implode(' ', $buffer) . PHP_EOL;
            }else{
                $str .= $buffer;
            }
        }
        if($this->file === false){
            $this->file = dirname(__FILE__) . '/combination'.$this->fieldsCount.'_'.$this->chipCount.'_'.time().'.txt';
        }
        file_put_contents($this->file, $str, FILE_APPEND);
        @chmod($this->file, 0776);
        $this->combination['buffer'] = [];
    }

    private function _searchRightOne($array) {
        for ($i = count($array) - 1; $i >= 0; $i--) {
            if ($array[$i] == 1) {
                return $i;
            }
        }

        return false;
    }

    public function getAllCombination($limit = 1) {
        if ((int) $limit > 0) {
            $this->bufferLimit = (int) $limit;
        }
        if ($this->countCombination >= 10) {
            $this->combination['buffer'][] = [$this->countCombination];
            $this->combination['last'] = $this->firstCombination;
            for ($i = 1; $i <= $this->countCombination; $i++) {
                $this->combination['buffer'][] = $this->combination['last'];

                if ($i % $this->bufferLimit == 0) {
                    $this->_saveInFile();
                }
                if ($i != $this->countCombination) {
                    $lastCount = count($this->combination['last']) - 1;
                    if ($this->combination['last'][$lastCount] == 0) {
                        $this->_shiftLastOne();
                    } else {
                        $this->_AddOneAfterLastOne();
                    }
                }
            }
            $this->_saveInFile();
        } else {
            $this->combination['buffer'][] = 'менее 10 вариантов';
            $this->_saveInFile();
        }
        return $this->file;
    }
}

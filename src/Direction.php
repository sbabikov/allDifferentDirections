<?php

namespace AllDifferentDirections;

/**
* Direction class
* @author Sergey Babikov
*/
class Direction
{
    const COMMAND_WALK = 'walk';
    const COMMAND_TURN = 'turn';
    const COMMAND_START = 'start';
    
    /** Input consists of up to 100 test cases **/
    const TEST_CASE_AMOUNT = 100;
    
    /** the number of people you ask for directions **/
    const PEOPLE_MIN_AMOUNT = 1;
    const PEOPLE_MAX_AMOUNT = 20;
    
    /** Each person’s directions contain at most n instructions **/
    const PERSON_INSTRUCTION_AMOUNT = 25;
    
    /** numeric inputs are real numbers in the range [−1000,1000] **/
    const NUMBER_MIN_RANGE = -1000;
    const NUMBER_MAX_RANGE = 1000;
    
    /**
    * Test cases
    * 
    * @param string $caseContent
    * @return string
    */
    public function testCase(string $caseContent): string
    {
        $sumX = 0;
        $sumY = 0;
            
        $peopleAmount = 0;
        $personCount = 1;
        $testCaseAmount = 0;
        $locationArray = [];
        
        $result = '';
        
        $caseRowArray = explode("\n", $caseContent);
        
        foreach ($caseRowArray as $caseRowString) {
            $caseRowString = trim($caseRowString);
            
            if ($caseRowString === '') {
                continue;
            }
            
            if ($personCount > $peopleAmount) {
                $peopleAmount = intval($caseRowString);
                $personCount = 1;
                
                if ($peopleAmount > $this::PEOPLE_MAX_AMOUNT) {
                    throw \OutOfRangeException(
                        'People number must be in range ' . 
                        $this::PEOPLE_MIN_AMOUNT .
                        '..' . 
                        $this::PEOPLE_MAX_AMOUNT
                    );
                }
                
                $testCaseAmount += $peopleAmount;
                
                if ($testCaseAmount > $this::TEST_CASE_AMOUNT) {
                    throw \OutOfRangeException(
                        'Test cases must be in range ' .
                        '1..' .$this::TEST_CASE_AMOUNT
                    );
                }
                
                continue;
            }
            
            $caseColArray = explode(' ', $caseRowString);
            
            list($x, $y) = $this->getPersonLocation($caseColArray);
            list($x, $y) = $this->applyCommands($caseColArray, $x, $y);
            
            $locationArray[] = [$x, $y];
            
            $sumX += $x;
            $sumY += $y;
            
            if ($peopleAmount === $personCount) {
                $averageX = $sumX / $peopleAmount;
                $averageY = $sumY / $peopleAmount;
                
                $result .= round($averageX, 4) . ' ' . 
                    round($averageY, 4) . ' ' .
                    round($this->getMaxStraightLineDistance($averageX, $averageY, $locationArray), 5) .
                    "\n";
                
                $sumX = 0;
                $sumY = 0;

                $locationArray = [];
            }
            
            ++ $personCount;
        }
        
        return $result;
    }
    
    /**
    * Get straight line distance
    * 
    * @param float $averageX
    * @param float $averageY
    * @param float $x
    * @param float $y
    * @return float
    */
    private function getStraightLineDistance(float $averageX, float $averageY, float $x, float $y): float
    {
        return sqrt(pow(($averageX - $x), 2) + pow(($averageY - $y), 2));
    }
    
    /**
    * Get max straight line distance
    * 
    * @param float $averageX
    * @param float $averageY
    * @param array $locationArray
    * @return float
    */
    private function getMaxStraightLineDistance(float $averageX, float $averageY, array $locationArray): float
    {
        $maxStraightLineDistance = 0;
        
        foreach ($locationArray as $location) {
            $straightLineDistance = $this->getStraightLineDistance($averageX, $averageY, $location[0], $location[1]);

            if ($straightLineDistance > $maxStraightLineDistance) {
                $maxStraightLineDistance = $straightLineDistance;
            }
        }

        return $maxStraightLineDistance;
    }
    
    /**
    * Get person location
    * 
    * @param array $caseColArray
    * @return array
    */
    private function getPersonLocation(array $caseColArray): array
    {
        $x = floatval($caseColArray[0]);
        $y = floatval($caseColArray[1]);
        
        $this->checkNumberRange($x);
        $this->checkNumberRange($y);
        
        return [$x, $y];
    }
    
    /**
    * Apply all commands of person and return location
    * 
    * @param array $caseColArray
    * @param float $x
    * @param float $y
    * @return array
    */
    private function applyCommands(array $caseColArray, float $x, float $y): array
    {
        $personInstructionAmount = count($caseColArray) / 2 - 2;
        
        if ($personInstructionAmount < 1 || $personInstructionAmount > $this::PERSON_INSTRUCTION_AMOUNT) {
            throw \OutOfRangeException(
                'Person\'s instructions must be in range ' .
                '1..' .$this::PERSON_INSTRUCTION_AMOUNT
            );
        }
        
        $angle = 0;
        
        for ($i = 2; $i < count($caseColArray); $i += 4) {
            $command = $caseColArray[$i];
            
            if ($i > 2 && $command === $this::COMMAND_START) {
                throw \UnexpectedValueException($this::COMMAND_START . ' command can not be here');
            }
            
            if (!in_array($command, [$this::COMMAND_START, $this::COMMAND_TURN])) {
                throw \UnexpectedValueException('Unexpected command: ' . $command);
            }
            
            $angle += $caseColArray[$i + 1];
            $nextCommand = $caseColArray[$i + 2];
            
            if ($nextCommand !== $this::COMMAND_WALK) {
                throw \UnexpectedValueException('Unexpected command: ' . $command);
            }
            
            $distance = floatval($caseColArray[$i + 3]);
            list($x, $y) = $this->doStep($x, $y, $angle, $distance);
        }
        
        return [$x, $y];
    }
    
    /**
    * Check number range
    * 
    * @param float $number
    */
    private function checkNumberRange(float $number)
    {
        if ($number < $this::NUMBER_MIN_RANGE || $number > $this::NUMBER_MAX_RANGE) {
            throw \OutOfRangeException(
                'Test cases must be in range ' .
                $this::NUMBER_MIN_RANGE .
                '..' .
                $this::NUMBER_MAX_RANGE
            );
        }
    }
    
    /**
    * Do one step
    * 
    * @param float $x
    * @param float $y
    * @param float $angle
    * @param float $distance
    * @return array
    */
    private function doStep(float $x, float $y, float $angle, float $distance): array
    {
        $this->checkNumberRange($angle);
        $this->checkNumberRange($distance);
        
        $x += $distance * cos(deg2rad($angle));
        $y += $distance * sin(deg2rad($angle));

        return [$x, $y];
    }
}

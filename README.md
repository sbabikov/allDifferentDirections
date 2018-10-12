# All Different Directions
#### https://open.kattis.com/problems/alldifferentdirections
If you walk through a big city and try to find your way around, you might try asking people for directions. However, asking nn people for directions might result in nn different sets of directions. But you believe in the law of averages: if you consider everyone’s advice, then you will have a good idea of where to go by computing the average destination that they all lead to. You would also like to know how far off were the worst directions. You compute this as the maximum straight-line distance between each direction’s destination and the averaged destination.

#### Requirements and depends
* PHP >= 5.5.9

## Install

* ``$ composer install``

## Uses

```php
    $direction = new Direction();

    echo $direction->testCase('3
87.342 34.30 start 0 walk 10.0
2.6762 75.2811 start -45.0 walk 40 turn 40.0 walk 60
58.518 93.508 start 270 walk 50 turn 90 walk 40 turn 13 walk 5
2
30 40 start 90 walk 5
40 50 start 180 walk 10 turn 90 walk 5
0
    ');

```

``$filePath`` - Path to exist file
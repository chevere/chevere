<?php

declare(strict_types=1);

function floatValues(float ...$values): array
{
    return $values;
}

function integerValues(int ...$values): array
{
    return $values;
}

vd(
    floatValues(1.1, 2.1, 3.1),
    integerValues(1, 2, 3),
    // This should throw TypeError, but converts int to float
    floatValues(1, 2, 3)
);

// This throws TypeError as expected
integerValues(1.1, 2.1, 3.1);

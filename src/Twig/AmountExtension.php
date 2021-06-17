<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class AmountExtension extends AbstractExtension
{

    public function getFilters()
    {
        return ([
            new TwigFilter('amount', [$this, 'amount'])
        ]);
    }
    public function amount($value, string $symbol = '€',  string $thousandsep = ' ', int $chifvirgu = 2, string $decsep = ',')
    {
        $finalValue = $value / 100;
        $finalValue = number_format($finalValue, $chifvirgu, $decsep, $thousandsep);

        return $finalValue . " $symbol";
    }
}

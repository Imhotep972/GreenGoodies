<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice(int $amount): string
    {
        // Convertit les centimes en euros
        $value = $amount / 100;

        // Format français : 2 décimales, virgule, espace
        return number_format($value, 2, ',', ' ') . ' €';
    }
}
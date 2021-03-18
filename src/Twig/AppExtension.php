<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('price', [$this, 'formatPrice']),
            new TwigFilter('badge', [$this, 'displayBadge'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hello', [$this, 'sayHello']),
        ];
    }

    public function formatPrice(float $price): string
    {
        return number_format($price, 2, ',', ' ') . ' €';
    }

    public function sayHello(string $name): string
    {
        return "Bonjour $name !";
    }

    public function displayBadge(array $product, string $type): string
    {
        switch($type) {
            case 'stock':
                return $product['stock'] > 0
                    ? '<span class="badge rounded-pill bg-success">Disponible</span>'
                    : '<span class="badge rounded-pill bg-danger">Écoulé</span>';
            default:
                return '';
        }
    }
}

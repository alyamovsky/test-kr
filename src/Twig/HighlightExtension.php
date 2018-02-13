<?php
declare(strict_types=1);

namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HighlightExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('highlight', [$this, 'highlightFilter']),
        ];
    }

    public function highlightFilter($content, $query)
    {
        $pattern = explode(' ', $query);
        foreach ($pattern as $item) {
            $content = preg_replace("~$item~", "<span class=\"highlighted\">$item</span>", $content);
        }

        return new \Twig_Markup($content, 'UTF-8');
    }
}
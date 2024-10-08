<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Material: string implements HasLabel
{
    case METAL = 'METAL';
    case ZIRCONIA = 'ZIRCONIA';
    case EMAX = 'EMAX';
    case PMMA = 'PMMA';
    case PROTOTYPE = 'PROTOTYPE';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::METAL => 'Metāls',
            self::ZIRCONIA => 'Cirkonijs',
            self::EMAX => 'Emax',
            self::PMMA => 'PMMA',
            self::PROTOTYPE => 'Prototips',
        };
    }
}

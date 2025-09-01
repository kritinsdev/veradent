<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Type: string implements HasLabel
{
    case FULL_FORM_IMPLANT = 'FULL_FORM_IMPLANT';
    case FULL_FORM_NO_IMPLANT = 'FULL_FORM_NO_IMPLANT';
    case REDUCED_FORM_IMPLANT = 'REDUCED_FORM_IMPLANT';
    case REDUCED_FORM_NO_IMPLANT = 'REDUCED_FORM_NO_IMPLANT';
    case WAX_UP = 'WAX_UP';
    case ONLAY = 'ONLAY';
    case PROTOTYPE = 'PROTOTYPE';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FULL_FORM_IMPLANT => 'Pilna forma - ar implantu',
            self::FULL_FORM_NO_IMPLANT => 'Pilna forma - bez implanta',
            self::REDUCED_FORM_IMPLANT => 'Reducēta forma - ar implantu',
            self::REDUCED_FORM_NO_IMPLANT => 'Reducēta forma - bez implanta',
            self::WAX_UP => 'Wax up',
            self::ONLAY => 'Onleja',
            self::PROTOTYPE => 'Prototips',
        };
    }

    public function price(): int
    {
        return match ($this) {
            self::FULL_FORM_IMPLANT => 12,
            self::FULL_FORM_NO_IMPLANT, self::ONLAY => 10,
            self::REDUCED_FORM_IMPLANT => 8,
            self::REDUCED_FORM_NO_IMPLANT => 5,
            self::WAX_UP => 3,
            self::PROTOTYPE => 2,
        };
    }
}

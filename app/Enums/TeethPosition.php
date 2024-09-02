<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TeethPosition: string implements HasLabel
{
    case D_11 = 'D_11';
    case D_12 = 'D_12';
    case D_13 = 'D_13';
    case D_14 = 'D_14';
    case D_15 = 'D_15';
    case D_16 = 'D_16';
    case D_17 = 'D_17';
    case D_18 = 'D_18';
    case D_21 = 'D_21';
    case D_22 = 'D_22';
    case D_23 = 'D_23';
    case D_24 = 'D_24';
    case D_25 = 'D_25';
    case D_26 = 'D_26';
    case D_27 = 'D_27';
    case D_28 = 'D_28';
    case D_31 = 'D_31';
    case D_32 = 'D_32';
    case D_33 = 'D_33';
    case D_34 = 'D_34';
    case D_35 = 'D_35';
    case D_36 = 'D_36';
    case D_37 = 'D_37';
    case D_38 = 'D_38';
    case D_41 = 'D_41';
    case D_42 = 'D_42';
    case D_43 = 'D_43';
    case D_44 = 'D_44';
    case D_45 = 'D_45';
    case D_46 = 'D_46';
    case D_47 = 'D_47';
    case D_48 = 'D_48';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::D_11 => 'D11',
            self::D_12 => 'D12',
            self::D_13 => 'D13',
            self::D_14 => 'D14',
            self::D_15 => 'D15',
            self::D_16 => 'D16',
            self::D_17 => 'D17',
            self::D_18 => 'D18',
            self::D_21 => 'D21',
            self::D_22 => 'D22',
            self::D_23 => 'D23',
            self::D_24 => 'D24',
            self::D_25 => 'D25',
            self::D_26 => 'D26',
            self::D_27 => 'D27',
            self::D_28 => 'D28',
            self::D_31 => 'D31',
            self::D_32 => 'D32',
            self::D_33 => 'D33',
            self::D_34 => 'D34',
            self::D_35 => 'D35',
            self::D_36 => 'D36',
            self::D_37 => 'D37',
            self::D_38 => 'D38',
            self::D_41 => 'D41',
            self::D_42 => 'D42',
            self::D_43 => 'D43',
            self::D_44 => 'D44',
            self::D_45 => 'D45',
            self::D_46 => 'D46',
            self::D_47 => 'D47',
            self::D_48 => 'D48',
        };
    }
}

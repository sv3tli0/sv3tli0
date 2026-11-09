<?php declare(strict_types=1);

namespace Profile;

enum Theme: string
{
    case Dark = 'dark';
    case Light = 'light';

    public function palette(): Palette
    {
        return match ($this) {
            self::Dark => new Palette(
                paper: '#12355C',
                gridMinor: '#17406C',
                gridMajor: '#1F4E82',
                ink: '#EEF4FB',
                muted: '#A9C4E2',
                red: '#FF8F7E',
                inkName: 'White ink',
            ),
            self::Light => new Palette(
                paper: '#F5F8FB',
                gridMinor: '#E6EDF5',
                gridMajor: '#D2DEEB',
                ink: '#1B4B7A',
                muted: '#5F80A4',
                red: '#C8402F',
                inkName: 'Blue ink',
            ),
        };
    }
}

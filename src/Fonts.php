<?php declare(strict_types=1);

namespace Profile;

use RuntimeException;

/** SVGs rendered through <img> cannot fetch web fonts, so the subsets are inlined. */
final readonly class Fonts
{
    public const array FACES = [
        ['Barlow', 400, 'BarlowSemiCondensed-Regular.woff2'],
        ['Barlow', 600, 'BarlowSemiCondensed-SemiBold.woff2'],
        ['Caveat', 500, 'Caveat.woff2'],
    ];

    private function __construct(public string $css) {}

    public static function embedded(string $dir): self
    {
        $css = '';
        foreach (self::FACES as [$family, $weight, $file]) {
            $bytes = file_get_contents("$dir/$file");
            if ($bytes === false) {
                throw new RuntimeException("Missing font: $dir/$file");
            }
            $css .= "@font-face{font-family:$family;font-weight:$weight;src:url(data:font/woff2;base64,"
                . base64_encode($bytes) . ") format('woff2')}";
        }

        return new self($css);
    }

    /** For sheets inlined into a page that already loads the fonts. */
    public static function none(): self
    {
        return new self('');
    }
}

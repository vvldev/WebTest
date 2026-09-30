<?php

declare(strict_types=1);

namespace App\Seed;

use GdImage;
use RuntimeException;

/**
 * Draws a random abstract cover picture for a post and stores it as JPEG.
 */
final class PostImageGenerator
{
    private const WIDTH = 1200;
    private const HEIGHT = 630;
    private const JPEG_QUALITY = 85;
    private const PATTERNS = ['circles', 'rectangles', 'triangles', 'stripes', 'waves'];
    private const GRADIENTS = ['vertical', 'horizontal', 'diagonal'];

    public function __construct(
        private readonly string $uploadDir,
        private readonly string $publicPath,
    ) {
    }

    /**
     * @return string path relative to public/, stored in the database
     */
    public function generate(): string
    {
        $image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        if ($image === false) {
            throw new RuntimeException('Cannot create image');
        }

        // Two hues far enough apart to make a visible gradient, from the whole color wheel.
        $hue = random_int(0, 359);
        $this->drawGradient(
            $image,
            $this->hslToRgb($hue, 0.7, 0.55),
            $this->hslToRgb(($hue + random_int(40, 160)) % 360, 0.65, 0.45),
            self::GRADIENTS[array_rand(self::GRADIENTS)],
        );
        $this->drawPattern($image, self::PATTERNS[array_rand(self::PATTERNS)]);

        $fileName = bin2hex(random_bytes(8)) . '.jpg';
        if (!imagejpeg($image, $this->uploadDir . '/' . $fileName, self::JPEG_QUALITY)) {
            throw new RuntimeException('Cannot save image to ' . $this->uploadDir);
        }

        return $this->publicPath . '/' . $fileName;
    }

    /**
     * Removes generated pictures only; .gitkeep and other files stay.
     */
    public function deleteAll(): void
    {
        $files = glob($this->uploadDir . '/*.jpg');
        foreach ($files === false ? [] : $files as $file) {
            unlink($file);
        }
    }

    /**
     * @param array{0: int, 1: int, 2: int} $from
     * @param array{0: int, 1: int, 2: int} $to
     */
    private function drawGradient(GdImage $image, array $from, array $to, string $direction): void
    {
        // Diagonal lines x - y = const cover every pixel, hence WIDTH + HEIGHT steps.
        $steps = match ($direction) {
            'vertical' => self::HEIGHT,
            'horizontal' => self::WIDTH,
            'diagonal' => self::WIDTH + self::HEIGHT,
        };
        for ($step = 0; $step < $steps; $step++) {
            $color = $this->mix($image, $from, $to, $step / ($steps - 1));
            match ($direction) {
                'vertical' => imageline($image, 0, $step, self::WIDTH - 1, $step, $color),
                'horizontal' => imageline($image, $step, 0, $step, self::HEIGHT - 1, $color),
                'diagonal' => imageline($image, $step, 0, $step - self::HEIGHT, self::HEIGHT, $color),
            };
        }
    }

    private function drawPattern(GdImage $image, string $pattern): void
    {
        $count = random_int(5, 9);
        for ($i = 0; $i < $count; $i++) {
            $color = $this->randomOverlayColor($image);
            match ($pattern) {
                'circles' => $this->drawCircle($image, $color),
                'rectangles' => $this->drawRectangle($image, $color),
                'triangles' => $this->drawTriangle($image, $color),
                'stripes' => $this->drawStripe($image, $color),
                'waves' => $this->drawWave($image, $color, $i, $count),
            };
        }
    }

    private function drawCircle(GdImage $image, int $color): void
    {
        $size = random_int(100, 520);
        imagefilledellipse($image, random_int(0, self::WIDTH), random_int(0, self::HEIGHT), $size, $size, $color);
    }

    private function drawRectangle(GdImage $image, int $color): void
    {
        $x = random_int(-100, self::WIDTH);
        $y = random_int(-100, self::HEIGHT);
        imagefilledrectangle($image, $x, $y, $x + random_int(80, 420), $y + random_int(80, 320), $color);
    }

    private function drawTriangle(GdImage $image, int $color): void
    {
        $points = [];
        for ($corner = 0; $corner < 3; $corner++) {
            $points[] = random_int(-100, self::WIDTH + 100);
            $points[] = random_int(-100, self::HEIGHT + 100);
        }
        imagefilledpolygon($image, $points, $color);
    }

    private function drawStripe(GdImage $image, int $color): void
    {
        $x = random_int(-self::HEIGHT, self::WIDTH);
        $width = random_int(30, 160);
        imagefilledpolygon($image, [
            $x, 0,
            $x + $width, 0,
            $x + $width + self::HEIGHT, self::HEIGHT,
            $x + self::HEIGHT, self::HEIGHT,
        ], $color);
    }

    /**
     * Waves are stacked from top to bottom so each one stays visible.
     */
    private function drawWave(GdImage $image, int $color, int $index, int $total): void
    {
        $baseline = (int) (self::HEIGHT * ($index + 1) / ($total + 1));
        $amplitude = random_int(15, 60);
        $frequency = random_int(1, 4) * M_PI / self::WIDTH;
        $phase = random_int(0, 628) / 100;

        $points = [];
        for ($x = 0; $x <= self::WIDTH; $x += 20) {
            $points[] = $x;
            $points[] = (int) ($baseline + $amplitude * sin($x * $frequency + $phase));
        }
        array_push($points, self::WIDTH, self::HEIGHT, 0, self::HEIGHT);
        imagefilledpolygon($image, $points, $color);
    }

    /**
     * Semi-transparent white or black, so shapes tint the gradient instead of hiding it.
     */
    private function randomOverlayColor(GdImage $image): int
    {
        $channel = random_int(0, 1) === 1 ? 255 : 0;

        // In GD alpha 0 is opaque and 127 fully transparent.
        return (int) imagecolorallocatealpha($image, $channel, $channel, $channel, random_int(85, 115));
    }

    /**
     * @param array{0: int, 1: int, 2: int} $from
     * @param array{0: int, 1: int, 2: int} $to
     */
    private function mix(GdImage $image, array $from, array $to, float $ratio): int
    {
        return (int) imagecolorallocate(
            $image,
            (int) round($from[0] + ($to[0] - $from[0]) * $ratio),
            (int) round($from[1] + ($to[1] - $from[1]) * $ratio),
            (int) round($from[2] + ($to[2] - $from[2]) * $ratio),
        );
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function hslToRgb(int $hue, float $saturation, float $lightness): array
    {
        $chroma = (1 - abs(2 * $lightness - 1)) * $saturation;
        $secondary = $chroma * (1 - abs(fmod($hue / 60, 2) - 1));
        $offset = $lightness - $chroma / 2;

        [$red, $green, $blue] = match (intdiv($hue, 60)) {
            0 => [$chroma, $secondary, 0],
            1 => [$secondary, $chroma, 0],
            2 => [0, $chroma, $secondary],
            3 => [0, $secondary, $chroma],
            4 => [$secondary, 0, $chroma],
            default => [$chroma, 0, $secondary],
        };

        return [
            (int) round(($red + $offset) * 255),
            (int) round(($green + $offset) * 255),
            (int) round(($blue + $offset) * 255),
        ];
    }
}

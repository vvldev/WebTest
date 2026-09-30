<?php

declare(strict_types=1);

namespace App\Enum;

enum PostSort: string
{
    case Date = 'date';
    case Views = 'views';

    public static function fromQuery(string $value): self
    {
        return self::tryFrom($value) ?? self::Date;
    }

    /**
     * Whitelist for ORDER BY: user input only picks a case, never reaches SQL.
     */
    public function orderByClause(): string
    {
        return match ($this) {
            self::Date => 'p.published_at DESC, p.id DESC',
            self::Views => 'p.views DESC, p.id DESC',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Date => 'По дате',
            self::Views => 'По просмотрам',
        };
    }
}

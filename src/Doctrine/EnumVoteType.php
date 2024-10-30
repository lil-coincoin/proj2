<?php

namespace App\Doctrine;

use App\Enum\VoteType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EnumVoteType extends Type
{
    public const NAME = 'enum_votetype';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        // Déclaration du type dans SQL (par exemple, une chaîne de 10 caractères)
        return "VARCHAR(10)";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?VoteType
    {
        // Convertir la valeur de la base de données en énumération PHP
        return $value !== null ? VoteType::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        // Convertir l'énumération PHP en valeur pour la base de données
        return $value instanceof VoteType ? $value->value : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
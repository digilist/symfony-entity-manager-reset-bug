<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity]
final class SomeEntity
{
    #[Id]
    #[Column]
    public int $id = 1;

    #[Column]
    public int $counter = 0;
}

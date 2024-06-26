<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
#[ORM\Table(name: "drivers")]
class Driver extends Base
{
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['trip', 'task', 'driver'])]
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

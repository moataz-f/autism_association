<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class ContentService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}

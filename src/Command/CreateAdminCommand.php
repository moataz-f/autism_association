<?php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin')]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setEmail('admin@autism.tn');
        $user->setNom('Administrateur');
        $user->setPrenom('Système');
        $user->setRoles(['ROLE_ADMIN']);
        
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin123');
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Admin créé avec succès!');
        $output->writeln('Email: admin@autism.tn');
        $output->writeln('Password: admin123');

        return Command::SUCCESS;
    }
}
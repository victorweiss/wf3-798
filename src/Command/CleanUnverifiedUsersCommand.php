<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CleanUnverifiedUsersCommand extends Command
{
    private $userRepository;
    private $em;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        $this->userRepository = $userRepository;
        $this->em = $em;
        parent::__construct();
    }

    protected static $defaultName = 'app:clean:unverified-users';
    protected static $defaultDescription = "Deletes users who haven't verified their email address after 7 days";

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $unverifiedUsers = $this->userRepository->findUnverifiedUsersToDelete();
        $count = count($unverifiedUsers);

        foreach ($unverifiedUsers as $user) {
            $io->info(sprintf("Deleting... %s (%s)", $user->getEmail(), $user->getId()));
            $this->em->remove($user);
        }

        $this->em->flush();

        $io->success("$count unverified users have successfully been deleted.");

        return Command::SUCCESS;
    }
}

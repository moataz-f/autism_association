<?php
namespace App\Command;

use App\Entity\PageContent;
use App\Entity\DonationCampaign;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:init-content')]
class InitContentCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Créer contenu Hero
        $hero = new PageContent();
        $hero->setSectionKey('hero');
        $hero->setTitre('جمعية حلم البراءة للتوحد');
        $hero->setSousTitre('معاً نصنع الأمل... معاً نحقق الحلم');
        $hero->setDescription('جمعية متخصصة في رعاية وتأهيل الأطفال ذوي اضطراب التوحد بقبلي');
        $hero->setActif(true);
        $this->em->persist($hero);

        // Créer contenu About
        $about = new PageContent();
        $about->setSectionKey('about');
        $about->setTitre('من نحن');
        $about->setSousTitre('جمعية حلم البراءة للتوحد بقبلي');
        $about->setDescription('نحو مستقبل أفضل لأطفالنا');
        $about->setActif(true);
        $this->em->persist($about);

        // Créer campagne de donation
        $campaign = new DonationCampaign();
        $campaign->setTitre('حملة تأهيل مركز الجمعية');
        $campaign->setDescription('نحتاج دعمكم لتجهيز وتأهيل المركز بالمعدات والأدوات اللازمة');
        $campaign->setMontantObjectif('50000');
        $campaign->setMontantCollecte('25000');
        $campaign->setActif(true);
        $campaign->setPrincipale(true);
        $this->em->persist($campaign);

        $this->em->flush();

        $output->writeln('✅ تم إنشاء المحتوى الافتراضي بنجاح!');
        return Command::SUCCESS;
    }
}
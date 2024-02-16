<?php
namespace App\Services;

use DateTime;
use App\Config;
use DateTimeZone;
use App\Entity\Visit;
use DateTimeImmutable;
use App\Repository\VisitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class VisitCounter
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $request,
        private VisitRepository $visitRepository
    )
    {

    }

    public function count($visitedPageId)
    {
        $visitsInSession = $this->getVisitsInSession();
    
        if(!in_array($visitedPageId, $visitsInSession))
        {
            $visitsInSession[] = $visitedPageId;
            $this->registerVisitsInSession($visitsInSession);

            if($this->visitRepository->count([]) >= Config::MAX_VISITS)
            {
                $this->em->remove(
                    $this->visitRepository->findOlder()
                );
                $this->em->flush();
            }

            $visit = new Visit;
            $visit->setVisitedPageId($visitedPageId)
                    ->setVisitedAt(new DateTimeImmutable('now', new DateTimeZone('Europe/Paris')))
                    ;
            $this->em->persist($visit);
            $this->em->flush();
        }
    }

    private function getVisitsInSession(): array
    {
        return $this->request->getSession()->get('visit', []);
    }
    private function registerVisitsInSession(array $visitsToRegister)
    {
        $this->request->getSession()->set('visit', $visitsToRegister);
    }
}
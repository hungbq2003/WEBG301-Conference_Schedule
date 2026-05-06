<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use App\Repository\AttendeeRepository;
use App\Repository\SpeakerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        $upcomingConferences = $conferenceRepository->findUpcomingConferences();
        $pastConferences = $conferenceRepository->findPastConferences();
        
        return $this->render('home/index.html.twig', [
            'upcoming_conferences' => $upcomingConferences,
            'past_conferences' => $pastConferences,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(
        ConferenceRepository $conferenceRepository,
        AttendeeRepository $attendeeRepository,
        SpeakerRepository $speakerRepository
    ): Response
    {
        $stats = [
            'total_conferences' => count($conferenceRepository->findAll()),
            'upcoming_conferences' => count($conferenceRepository->findUpcomingConferences()),
            'total_attendees' => count($attendeeRepository->findAll()),
            'total_speakers' => count($speakerRepository->findAll()),
        ];

        return $this->render('home/dashboard.html.twig', ['stats' => $stats]);
    }
}

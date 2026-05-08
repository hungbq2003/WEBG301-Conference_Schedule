<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use App\Repository\SessionRepository;
use App\Repository\SpeakerRepository;
use App\Repository\AttendeeRepository;
use App\Validation\InputValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/conferences')]
class ConferenceController extends AbstractController
{
    #[Route('', name: 'app_conferences_list')]
    public function list(Request $request, ConferenceRepository $conferenceRepository): Response
    {
        $query = trim((string) $request->query->get('q', ''));
        $conferences = $conferenceRepository->search($query);

        return $this->render('conference/list.html.twig', [
            'conferences' => $conferences,
            'query' => $query,
        ]);
    }

    #[Route('/new', name: 'app_conference_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $errors = array_merge(
                InputValidator::validateConferenceDates($request->request->get('startDate'), $request->request->get('endDate')),
                InputValidator::validateCapacity($request->request->get('capacity'))
            );
            if ($errors) {
                foreach ($errors as $msg) {
                    $this->addFlash('error', $msg);
                }
                return $this->redirectToRoute('app_conference_new');
            }

            $conference = new Conference();
            $conference->setName($request->request->get('name'));
            $conference->setDescription($request->request->get('description'));
            $conference->setLocation($request->request->get('location'));
            $conference->setCapacity((int) $request->request->get('capacity', 100));
            $conference->setStartDate(new \DateTime($request->request->get('startDate')));
            $conference->setEndDate(new \DateTime($request->request->get('endDate')));

            $entityManager->persist($conference);
            $entityManager->flush();

            $this->addFlash('success', 'Conference successfully created.');
            return $this->redirectToRoute('app_conference_show', ['id' => $conference->getId()]);
        }

        return $this->render('conference/form.html.twig', [
            'conference' => null,
            'action' => 'Create Conference',
            'formAction' => $this->generateUrl('app_conference_new'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conference_edit', requirements: ['id' => '\d+'])]
    public function edit(Conference $conference, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $errors = array_merge(
                InputValidator::validateConferenceDates($request->request->get('startDate'), $request->request->get('endDate')),
                InputValidator::validateCapacity($request->request->get('capacity'))
            );
            if ($errors) {
                foreach ($errors as $msg) {
                    $this->addFlash('error', $msg);
                }
                return $this->redirectToRoute('app_conference_edit', ['id' => $conference->getId()]);
            }

            $conference->setName($request->request->get('name'));
            $conference->setDescription($request->request->get('description'));
            $conference->setLocation($request->request->get('location'));
            $conference->setCapacity((int) $request->request->get('capacity', 100));
            $conference->setStartDate(new \DateTime($request->request->get('startDate')));
            $conference->setEndDate(new \DateTime($request->request->get('endDate')));

            $entityManager->flush();

            $this->addFlash('success', 'Conference successfully updated.');
            return $this->redirectToRoute('app_conference_show', ['id' => $conference->getId()]);
        }

        return $this->render('conference/form.html.twig', [
            'conference' => $conference,
            'action' => 'Edit Conference',
            'formAction' => $this->generateUrl('app_conference_edit', ['id' => $conference->getId()]),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_conference_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Conference $conference, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($conference);
        $entityManager->flush();

        $this->addFlash('success', 'Conference deleted successfully.');
        return $this->redirectToRoute('app_conferences_list');
    }

    #[Route('/{id}', name: 'app_conference_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(
        Conference $conference,
        SessionRepository $sessionRepository,
        SpeakerRepository $speakerRepository,
        AttendeeRepository $attendeeRepository
    ): Response
    {
        $sessions = $sessionRepository->findByConference($conference);
        $speakers = $speakerRepository->findByConference($conference);
        $attendeeCount = $attendeeRepository->countByConference($conference);

        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'sessions' => $sessions,
            'speakers' => $speakers,
            'attendee_count' => $attendeeCount,
        ]);
    }

    #[Route('/{id}/schedule', name: 'app_conference_schedule', requirements: ['id' => '\d+'])]
    public function schedule(Conference $conference, SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findByConference($conference);

        return $this->render('conference/schedule.html.twig', [
            'conference' => $conference,
            'sessions' => $sessions,
        ]);
    }

    #[Route('/{id}/speakers', name: 'app_conference_speakers', requirements: ['id' => '\d+'])]
    public function speakers(Conference $conference, SpeakerRepository $speakerRepository): Response
    {
        $speakers = $speakerRepository->findByConference($conference);

        return $this->render('conference/speakers.html.twig', [
            'conference' => $conference,
            'speakers' => $speakers,
        ]);
    }

    #[Route('/{id}/statistics', name: 'app_conference_statistics', requirements: ['id' => '\d+'])]
    public function statistics(
        Conference $conference,
        SessionRepository $sessionRepository,
        SpeakerRepository $speakerRepository,
        AttendeeRepository $attendeeRepository
    ): Response
    {
        $stats = [
            'total_sessions' => count($sessionRepository->findByConference($conference)),
            'total_speakers' => count($speakerRepository->findByConference($conference)),
            'total_attendees' => $attendeeRepository->countByConference($conference),
            'capacity' => $conference->getCapacity(),
        ];

        return $this->render('conference/statistics.html.twig', [
            'conference' => $conference,
            'stats' => $stats,
        ]);
    }
}

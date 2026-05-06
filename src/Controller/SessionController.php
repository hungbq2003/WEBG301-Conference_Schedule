<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\Session;
use App\Repository\ConferenceRepository;
use App\Repository\SessionRepository;
use App\Repository\AttendeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sessions')]
class SessionController extends AbstractController
{
    #[Route('', name: 'app_sessions_list')]
    public function list(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findAll();

        return $this->render('session/list.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/new', name: 'app_session_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ConferenceRepository $conferenceRepository
    ): Response
    {
        $conferences = $conferenceRepository->findAll();

        if ($request->isMethod('POST')) {
            $conferenceId = (int) $request->request->get('conference');
            $conference = $conferenceRepository->find($conferenceId);

            $session = new Session();
            $session->setTitle($request->request->get('title'));
            $session->setDescription($request->request->get('description'));
            $session->setRoom($request->request->get('room'));
            $session->setTrack($request->request->get('track'));
            $session->setStartTime(new \DateTime($request->request->get('startTime')));
            $session->setEndTime(new \DateTime($request->request->get('endTime')));
            $session->setCapacity((int) $request->request->get('capacity', 100));
            $session->setConference($conference ?? $conferences[0]);

            $entityManager->persist($session);
            $entityManager->flush();

            $this->addFlash('success', 'Session successfully created.');
            return $this->redirectToRoute('app_session_show', ['id' => $session->getId()]);
        }

        return $this->render('session/form.html.twig', [
            'session' => null,
            'conferences' => $conferences,
            'action' => 'Create Session',
            'formAction' => $this->generateUrl('app_session_new'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_session_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Session $session,
        Request $request,
        EntityManagerInterface $entityManager,
        ConferenceRepository $conferenceRepository
    ): Response
    {
        $conferences = $conferenceRepository->findAll();
        if ($request->isMethod('POST')) {
            $conferenceId = (int) $request->request->get('conference');
            $conference = $conferenceRepository->find($conferenceId);

            $session->setTitle($request->request->get('title'));
            $session->setDescription($request->request->get('description'));
            $session->setRoom($request->request->get('room'));
            $session->setTrack($request->request->get('track'));
            $session->setStartTime(new \DateTime($request->request->get('startTime')));
            $session->setEndTime(new \DateTime($request->request->get('endTime')));
            $session->setCapacity((int) $request->request->get('capacity', 100));
            $session->setConference($conference ?? $session->getConference());

            $entityManager->flush();

            $this->addFlash('success', 'Session successfully updated.');
            return $this->redirectToRoute('app_session_show', ['id' => $session->getId()]);
        }

        return $this->render('session/form.html.twig', [
            'session' => $session,
            'conferences' => $conferences,
            'action' => 'Edit Session',
            'formAction' => $this->generateUrl('app_session_edit', ['id' => $session->getId()]),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_session_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Session $session, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($session);
        $entityManager->flush();

        $this->addFlash('success', 'Session deleted successfully.');
        return $this->redirectToRoute('app_sessions_list');
    }

    #[Route('/{id}', name: 'app_session_show', requirements: ['id' => '\d+'])]
    public function show(Session $session): Response
    {
        $attendees = $session->getAttendees();
        $availableSeats = $session->getAvailableSeats();

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'attendees' => $attendees,
            'available_seats' => $availableSeats,
        ]);
    }

    #[Route('/{id}/attendees', name: 'app_session_attendees', requirements: ['id' => '\d+'])]
    public function attendees(Session $session): Response
    {
        $attendees = $session->getAttendees();

        return $this->render('session/attendees.html.twig', [
            'session' => $session,
            'attendees' => $attendees,
        ]);
    }
}

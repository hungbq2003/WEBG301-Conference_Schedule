<?php

namespace App\Controller;

use App\Entity\Attendee;
use App\Entity\Conference;
use App\Repository\AttendeeRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/attendees')]
class AttendeeController extends AbstractController
{
    #[Route('', name: 'app_attendees_list')]
    public function list(AttendeeRepository $attendeeRepository): Response
    {
        $attendees = $attendeeRepository->findAll();

        return $this->render('attendee/list.html.twig', [
            'attendees' => $attendees,
        ]);
    }

    #[Route('/new', name: 'app_attendee_new')]
    public function new(Request $request, ConferenceRepository $conferenceRepository, EntityManagerInterface $entityManager): Response
    {
        $conferences = $conferenceRepository->findAll();

        if ($request->isMethod('POST')) {
            $conferenceId = (int) $request->request->get('conference');
            $conference = $conferenceRepository->find($conferenceId);

            $attendee = new Attendee();
            $attendee->setFirstName($request->request->get('firstName'));
            $attendee->setLastName($request->request->get('lastName'));
            $attendee->setEmail($request->request->get('email'));
            $attendee->setPhone($request->request->get('phone'));
            $attendee->setCompany($request->request->get('company'));
            $attendee->setJobTitle($request->request->get('jobTitle'));
            $attendee->setTicketType($request->request->get('ticketType', 'standard'));
            $attendee->setConference($conference ?? $conferences[0]);

            $entityManager->persist($attendee);
            $entityManager->flush();

            $this->addFlash('success', 'Attendee successfully registered.');
            return $this->redirectToRoute('app_attendees_list');
        }

        return $this->render('attendee/form.html.twig', [
            'attendee' => null,
            'conferences' => $conferences,
            'action' => 'Register Attendee',
            'formAction' => $this->generateUrl('app_attendee_new'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_attendee_edit', requirements: ['id' => '\d+'])]
    public function edit(Attendee $attendee, Request $request, ConferenceRepository $conferenceRepository, EntityManagerInterface $entityManager): Response
    {
        $conferences = $conferenceRepository->findAll();

        if ($request->isMethod('POST')) {
            $conferenceId = (int) $request->request->get('conference');
            $conference = $conferenceRepository->find($conferenceId);

            $attendee->setFirstName($request->request->get('firstName'));
            $attendee->setLastName($request->request->get('lastName'));
            $attendee->setEmail($request->request->get('email'));
            $attendee->setPhone($request->request->get('phone'));
            $attendee->setCompany($request->request->get('company'));
            $attendee->setJobTitle($request->request->get('jobTitle'));
            $attendee->setTicketType($request->request->get('ticketType', 'standard'));
            $attendee->setConference($conference ?? $attendee->getConference());

            $entityManager->flush();

            $this->addFlash('success', 'Attendee updated successfully.');
            return $this->redirectToRoute('app_attendee_show', ['id' => $attendee->getId()]);
        }

        return $this->render('attendee/form.html.twig', [
            'attendee' => $attendee,
            'conferences' => $conferences,
            'action' => 'Edit Attendee',
            'formAction' => $this->generateUrl('app_attendee_edit', ['id' => $attendee->getId()]),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_attendee_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Attendee $attendee, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($attendee);
        $entityManager->flush();

        $this->addFlash('success', 'Attendee deleted successfully.');
        return $this->redirectToRoute('app_attendees_list');
    }

    #[Route('/{id}', name: 'app_attendee_show', requirements: ['id' => '\d+'])]
    public function show(Attendee $attendee): Response
    {
        return $this->render('attendee/show.html.twig', [
            'attendee' => $attendee,
        ]);
    }

    #[Route('/conference/{conferenceId}', name: 'app_attendees_by_conference', requirements: ['conferenceId' => '\d+'])]
    public function byConference(
        int $conferenceId,
        ConferenceRepository $conferenceRepository,
        AttendeeRepository $attendeeRepository
    ): Response
    {
        $conference = $conferenceRepository->find($conferenceId);
        if (!$conference) {
            throw $this->createNotFoundException('Conference not found');
        }

        $attendees = $attendeeRepository->findByConference($conference);

        return $this->render('attendee/by_conference.html.twig', [
            'conference' => $conference,
            'attendees' => $attendees,
        ]);
    }

    #[Route('/registration/{conferenceId}', name: 'app_attendee_register', requirements: ['conferenceId' => '\d+'])]
    public function register(
        int $conferenceId,
        Request $request,
        ConferenceRepository $conferenceRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $conference = $conferenceRepository->find($conferenceId);
        if (!$conference) {
            throw $this->createNotFoundException('Conference not found');
        }

        if ($request->isMethod('POST')) {
            $attendee = new Attendee();
            $attendee->setFirstName($request->request->get('firstName'));
            $attendee->setLastName($request->request->get('lastName'));
            $attendee->setEmail($request->request->get('email'));
            $attendee->setPhone($request->request->get('phone'));
            $attendee->setCompany($request->request->get('company'));
            $attendee->setJobTitle($request->request->get('jobTitle'));
            $attendee->setTicketType($request->request->get('ticketType', 'standard'));
            $attendee->setConference($conference);

            $entityManager->persist($attendee);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully registered for the conference!');
            return $this->redirectToRoute('app_conference_show', ['id' => $conference->getId()]);
        }

        return $this->render('attendee/register.html.twig', [
            'conference' => $conference,
        ]);
    }

    #[Route('/{id}/checkin', name: 'app_attendee_checkin', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function checkIn(
        Attendee $attendee,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        if (!$attendee->isCheckedIn()) {
            $attendee->setCheckedIn(true);
            $entityManager->flush();
            $this->addFlash('success', $attendee->getFullName() . ' has been checked in!');
        }

        $referrer = $request->headers->get('referer');
        return $this->redirect($referrer ?: $this->generateUrl('app_attendees_list'));
    }
}

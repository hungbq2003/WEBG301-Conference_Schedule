<?php

namespace App\Controller;

use App\Entity\Speaker;
use App\Repository\SpeakerRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/speakers')]
class SpeakerController extends AbstractController
{
    #[Route('', name: 'app_speakers_list')]
    public function list(SpeakerRepository $speakerRepository): Response
    {
        $speakers = $speakerRepository->findAll();

        return $this->render('speaker/list.html.twig', [
            'speakers' => $speakers,
        ]);
    }

    #[Route('/new', name: 'app_speaker_new')]
    public function new(Request $request, ConferenceRepository $conferenceRepository, EntityManagerInterface $entityManager): Response
    {
        $conferences = $conferenceRepository->findAll();

        if ($request->isMethod('POST')) {
            $conferenceId = (int) $request->request->get('conference');
            $conference = $conferenceRepository->find($conferenceId);

            $speaker = new Speaker();
            $speaker->setFirstName($request->request->get('firstName'));
            $speaker->setLastName($request->request->get('lastName'));
            $speaker->setEmail($request->request->get('email'));
            $speaker->setPhone($request->request->get('phone'));
            $speaker->setBio($request->request->get('bio'));
            $speaker->setAffiliation($request->request->get('affiliation'));
            $speaker->setProfileImage($request->request->get('profileImage'));
            $speaker->setConference($conference ?? $conferences[0]);

            $entityManager->persist($speaker);
            $entityManager->flush();

            $this->addFlash('success', 'Speaker successfully created.');
            return $this->redirectToRoute('app_speaker_show', ['id' => $speaker->getId()]);
        }

        return $this->render('speaker/form.html.twig', [
            'speaker' => null,
            'conferences' => $conferences,
            'action' => 'Add Speaker',
            'formAction' => $this->generateUrl('app_speaker_new'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_speaker_edit', requirements: ['id' => '\d+'])]
    public function edit(Speaker $speaker, Request $request, ConferenceRepository $conferenceRepository, EntityManagerInterface $entityManager): Response
    {
        $conferences = $conferenceRepository->findAll();

        if ($request->isMethod('POST')) {
            $conferenceId = (int) $request->request->get('conference');
            $conference = $conferenceRepository->find($conferenceId);

            $speaker->setFirstName($request->request->get('firstName'));
            $speaker->setLastName($request->request->get('lastName'));
            $speaker->setEmail($request->request->get('email'));
            $speaker->setPhone($request->request->get('phone'));
            $speaker->setBio($request->request->get('bio'));
            $speaker->setAffiliation($request->request->get('affiliation'));
            $speaker->setProfileImage($request->request->get('profileImage'));
            $speaker->setConference($conference ?? $speaker->getConference());

            $entityManager->flush();

            $this->addFlash('success', 'Speaker successfully updated.');
            return $this->redirectToRoute('app_speaker_show', ['id' => $speaker->getId()]);
        }

        return $this->render('speaker/form.html.twig', [
            'speaker' => $speaker,
            'conferences' => $conferences,
            'action' => 'Edit Speaker',
            'formAction' => $this->generateUrl('app_speaker_edit', ['id' => $speaker->getId()]),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_speaker_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Speaker $speaker, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($speaker);
        $entityManager->flush();

        $this->addFlash('success', 'Speaker deleted successfully.');
        return $this->redirectToRoute('app_speakers_list');
    }

    #[Route('/{id}', name: 'app_speaker_show', requirements: ['id' => '\d+'])]
    public function show(Speaker $speaker): Response
    {
        return $this->render('speaker/show.html.twig', [
            'speaker' => $speaker,
        ]);
    }
}

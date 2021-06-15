<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/note")
 */
class NoteController extends AbstractController
{
    /**
     * @Route("/note_all", name="note_all", methods={"GET"})
     */
    public function index(NoteRepository $noteRepository): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $noteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/add_note", name="note_add", methods={"GET","POST"})
     */
    public function addNote(Request $request, ValidatorInterface $validator): Response
    {
        $note = new Note();
        // 2 méthodes disponibles : 
        // -1 : à partir d'un formulaire
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('note_all');
        }

        return $this->render('note/add_note.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
        ]);
        // -2 : à partir des données fournies par la requête
        /*
        $entityManager = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->all();
        $note = new Note();

        $note->setValue($data['value']);
        $note->setField($data['field']);
        $studentId = $student->getId();
        $note->setStudent($studentId);

        $errors = $validator->validate($note);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        $entityManager->persist($note);

        $entityManager->flush();

        return new Response('Saved new note with id '.$note->getId());
        */
    }

    /**
     * @Route("/note/{id}", name="get_note", methods={"GET"})
     */
    public function getNote(Note $note): Response
    {
        /* dans ce cas, au lieu de Note dans les paramètres on récupère id
        $note = $this->getDoctrine()
            ->getRepository(Note::class)
            ->find($id);

        if (!$note) {
            throw $this->createNotFoundException(
                'No note found for id '.$id
            );
        }

        return new Response('Note: '.$note);
        */
        return $this->render('note/get_note.html.twig', [
            'note' => $note,
        ]);
    }

    /**
     * @Route("/note/{id}/update", name="note_edit", methods={"GET","POST"})
     */
    public function updateNote(Request $request, Note $note): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('note_crud_index');
        }

        return $this->render('note/update.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
        ]);

        // autre possibilité
        /*
        $entityManager = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->all();
        $note = $this->getDoctrine()
            ->getRepository(Note::class)
            ->find($id);

        $note->setValue($data['value']);
        $note->setField($data['field']);
        $studentId = $student->getId();
        $note->setStudent($studentId);

        $entityManager->persist($note);

        $entityManager->flush();
        */
    }

    /**
     * @Route("/note/{id}/delete", name="note_delete", methods={"POST"})
     */
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return $this->redirectToRoute('note_crud_index');

        // autre possibilité
        /* $note = $this->getDoctrine()
        ->getRepository(Note::class)
        ->find($id);

        if (!$note) {
            throw $this->createNotFoundException(
                'No note found for id '.$id
            );
        }

        $entityManager->remove($note);
        $entityManager->flush();
        return new Response('Note deleted);
        */
    }

    /**
     * @Route("/note_average/{id}", name="note_average", methods={"GET"})
     */
    public function getAverageNote(NoteRepository $noteRepository, Request $request, int $id): Response
    {
        $allNotesForStudent = $noteRepository->findAllNotesForStudent($id);
        $allNotesSum = array_sum($allNotesForStudent);
        $average = $allNotesSum/count($allNotesSum);
        return $average;
    }

    /**
     * @Route("/note_average_class", name="note_average_class", methods={"GET"})
     */
    public function getAverageNoteForClass(NoteRepository $noteRepository, StudentRepository $studentRepository, Request $request): Response
    {
        $allStudents = $studentRepository->findAll();
        foreach($allStudents as $student){
            $allNotesForStudent = $noteRepository->findAllNotesForStudent($id);
            $allNotesSum += array_sum($allNotesForStudent);
            $numberOfNotes += count($allNotesForStudent);
        }
        $averageClass = $allNotesSum/$numberOfNotes;
        return $averageClass;
    }
}

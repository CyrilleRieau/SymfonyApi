<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/student_all", name="student_all", methods={"GET"})
     */
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $studentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/add_student", name="add_student", methods={"GET","POST"})
     */
    public function addStudent(Request $request): Response
    {
        $student = new Student();
        // 2 méthodes disponibles : 
        // -1 : à partir d'un formulaire
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('student_index');
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);

        // -2 : à partir des données fournies par la requête
        /*
        $entityManager = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->all();
        $student = new Student();

        $student->setName($data['name']);
        $student->setFirstname($data['firstName']);
        $student->setBirthdate($data['birthdate']);

        $entityManager->persist($student);

        $entityManager->flush();

        return new Response('Saved new student with id '.$student->getId());
        */
    }

    /**
     * @Route("/student/{id}", name="get_student", methods={"GET"})
     */
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);

        /*
        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->find($id);

        if (!$student) {
            throw $this->createNotFoundException(
                'No student found for id '.$id
            );
        }

        return new Response('Student: '.$student);
        */

    }

    /**
     * @Route("/student/{id}/update", name="student_edit", methods={"GET","POST"})
     */
    public function updateStudent(Request $request, Student $student): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('student_index');
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);

        // autre possibilité
        /*
        $entityManager = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->all();
        $student = $this->getDoctrine()
            ->getRepository(Student::class)
            ->find($id);

        $student->setName($data['name']);
        $student->setFirstname($data['firstname']);
        $student->setBirthdate($birthdate);

        $entityManager->persist($student);

        $entityManager->flush();
        */

    }

    /**
     * @Route("/student/{id}/delete", name="student_delete", methods={"POST"})
     */
    public function delete(Request $request, Student $student): Response
    {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_index');

        // autre possibilité
        /* $student = $this->getDoctrine()
        ->getRepository(Student::class)
        ->find($id);

        if (!$student) {
            throw $this->createNotFoundException(
                'No student found for id '.$id
            );
        }

        $entityManager->remove($student);
        $entityManager->flush();
        return new Response('Student deleted);
        */

    }
}

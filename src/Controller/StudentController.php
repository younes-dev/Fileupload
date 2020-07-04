<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    /**
     * @Route("/", name="student")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UploaderHelper $uploaderHelper
     * @return RedirectResponse|Response
     */
    public function index(Request $request, EntityManagerInterface $manager, UploaderHelper $uploaderHelper)
    {
        $studentList= $manager->getRepository(Student::class)->findAll();

        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$uploadedFile = $form['imageFile']->getData();
            $uploadedFile = $student->getImage();
            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadImage($uploadedFile);
                $student->setImage($newFilename);
            }
            $manager->persist($student);
            $manager->flush();

            $this->addFlash('success', 'Article Created! Knowledge is power!');

            return $this->redirectToRoute('student');
        }
            return $this->render('student/index.html.twig', [
                'formStudent' => $form->createView(),
                "studentList" => $studentList

            ]);

    }

    /**
     * @Route("/show",name="show_list")
     * @param EntityManagerInterface $manager
     * @param Student $student
     */
    public function show(EntityManagerInterface $manager)
    {
        $studentList= $manager->getRepository(Student::class)->findAll();

        return $this->render("student/show.html.twig",[
            "studentList" => $studentList
        ]);

    }


    /**
     * @Route("/delete/{id}", name="delete_student",methods="GET")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param int $id
     * @return RedirectResponse
     */
    public function deleteStudent(Request $request,EntityManagerInterface $manager,int $id){

        $student =$manager->getRepository(Student::class)->find($id);
        $path=$this->getParameter("upload_directory").'/'.$student->getImage();
        if(file_exists($path)) {
            unlink($path);
            ////*******************************************************************************
            // this methode could remove the image from folder the same way do Unlink Function
            //*******************************************************************************
            //$filesystem = new Filesystem();
            //$filesystem->remove($path);
            //********************************
            }
        $manager->remove($student);
        $manager->flush();
        $this->addFlash('success','Soumission supprimer !!');
        return $this->redirectToRoute('student');
    }




    /**
     * @Route("/edit/{id}", name="edit_student")
     * @param int $id
     * @param Request $request
     * @param Student $student
     * @param EntityManagerInterface $manager
     * @param UploaderHelper $uploaderHelper
     * @return RedirectResponse|Response
     */

    public function edit(int $id,Request $request, Student $student,EntityManagerInterface $manager,UploaderHelper $uploaderHelper){

        $FileNamePath = $this->getParameter("upload_directory").'/'.$student->getImage();

        $form=$this->createForm(StudentType::class,$student);
        $form->handleRequest($request);

//        $fN = $student->getImage()->getClientOriginalName();


        if ($form->isSubmitted() && $form->isValid()){
            //dump($fN);die;
            if(file_exists($FileNamePath)){
                unlink($FileNamePath);
                $manager->remove($student);
            }
            $newFilename = $uploaderHelper->uploadImage($student->getImage());
            $student->setImage($newFilename);
            $manager->persist($student);
            $manager->flush();

            return $this->redirectToRoute('student');
        }

        return $this->render('student/edit.html.twig', [
            'formStudent' => $form->createView()
        ]);
    }
    


//    /**
//     * @Route("/delete/{id}", name="delete_student")
//     * @param Request $request
//     * @param $id
//     * @return RedirectResponse
//     */
//    public function deleteAction(Request $request, int $id)
//    {
//        $repository = $this->getDoctrine()->getRepository(Student::class);
//        $student = $repository->find($id);
//        $manager = $this->getDoctrine()->getManager();
//        $manager->remove($student);
//        $manager->flush();
//
//        return $this->redirectToRoute('show_list');
//    }


}



//*******************************************************************************
//***************************        First methode       ************************
//*******************************************************************************
//            $uploadedFile=$student->getImage();
//            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
//            $fileRename=$originalFilename.'_'.md5(uniqid()).'.'.$uploadedFile->guessExtension();
//            $uploadedFile->move($this->getParameter('upload_directory'), $fileRename);
//            $student->setImage($fileRename);
//            $manager->persist($student);
//            $manager->flush();
//*******************************************************************************
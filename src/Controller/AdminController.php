<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\Service;
use App\Form\MedecinType;
use App\Form\ServiceType;
use App\Entity\Specialite;
use App\Form\SpecialiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="showService")
     */
    public function show()
    {   $repo=$this->getDoctrine()->getRepository(Service::class);
        $service=$repo->findAll();
        return $this->render('admin/index.html.twig',['services'=>$service]);
    }
    /**
     * @Route("/admin/edit/{id}", name="edit")
     * @Route("/admin/new", name="new")
     */
    public function new(Request $requete,EntityManagerInterface $manager,Service $service=null){
        if (!$service) {
            $service= new Service();
        }
        $form=$this->createForm(ServiceType::class,$service);
        $form->handleRequest($requete);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($service);
            $manager->flush();
            return $this->redirectToRoute('showService');
        }
        return $this->render('admin/form.html.twig',['form'=>$form->createView(),'id'=>$service->getId()!==null]);
    }
    /**
     * @Route("/admin/delete/{id}", name="delete")
     */
    public function delete(EntityManagerInterface $manager,$id,Service $service){ 
            $repo=$this->getDoctrine()->getRepository(Service::class);
            $id=$repo->find($id);      
            $manager->remove($service);
            $manager->flush();
            return $this->redirectToRoute('showService'); 
    }
    /**
     * @Route("/admin/medecins",name="showMedecin")
     */
    public function showMedecin(){
        $repo=$this->getDoctrine()->getRepository(Medecin::class);
        $medecin=$repo->findAll();
        return $this->render('admin/medecin.html.twig',['medecins'=>$medecin]);
    }
    /**
     * @Route("/admin/newMedecin",name="newMedecin")
     * @Route("/admin/medecin/edit/{id}",name="editMedecin")
     */
    public function newMedecin(Request $requete,EntityManagerInterface $manager,Medecin $medecin=null){
        if (!$medecin) {
            $medecin=new Medecin();
        }
        $form=$this->createForm(MedecinType::class,$medecin);
        $form->handleRequest($requete);
        if ($form->isSubmitted() && $form->isValid()) {
            $lastId=$medecin->getId();
            if (!$medecin->getId()) {
                   $repo=$this->getDoctrine()->getRepository(Medecin::class);
                   dump($medecinId=$repo->findAll());
                   $lastRecord=count($medecinId);
                   $lastObject=$medecinId[$lastRecord-1];
                   $lastId=$lastObject->getId()+1;
            }
            $manager->persist($medecin);
            $serviceSubstr= substr($medecin->getService()->getLibelle(),0,2);
            $medecin->setMatricule('M'.$serviceSubstr.$concat=(strlen($lastId)<5)?str_pad($lastId,5, "0", STR_PAD_LEFT):$lastId);
            $manager->flush();
            return $this->redirectToRoute('showMedecin');
        }
        return $this->render('admin/formMedecin.html.twig',['form'=>$form->createView(),'id'=>$medecin->getId()!==null]);
    }
    /**
     * @Route("/admin/medecin/delete/{id}",name="deleteMedecin")
     */
    public function deleteMedecin(EntityManagerInterface $manager,Medecin $medecin,$id){
        $repo=$this->getDoctrine()->getRepository(Medecin::class);
        $id=$repo->find($id);
        $manager->remove($medecin);
        $manager->flush();
        return $this->redirectToRoute('showMedecin');

    }
    /**
     * @Route("/admin/specialite", name="showSpecialite")
     */
    public function showSpecialite(){
        $repo=$this->getDoctrine()->getRepository(Specialite::class);
        $specialites=$repo->findAll();
        return $this->render('admin/specialites.html.twig',['specialites'=>$specialites]);
    }
    /**
     * @Route("/admin/specialite/edit/{id}",name="editSpecialite")
     * @Route("/admin/specialite/newSpecialite",name="newSpeciaite")
     */
    public function newSpecialite(Request $requete,EntityManagerInterface $manager,Specialite $specialite=null){
        if (!$specialite) {
            $specialite= new Specialite();
        }
        $form=$this->createForm(SpecialiteType::class,$specialite);
        $form->handleRequest($requete);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($specialite);
            $manager->flush();
            return $this->redirectToRoute('showSpecialite');
        }
        return $this->render('admin/formSpecialite.html.twig',['form'=>$form->createView(),'id'=>$specialite->getId()!==null]);
    }
    /**
     * @Route("/admin/specialite/delete/{id}", name="deleteSpecialite")
     */
    public function deleteSpecialite(EntityManagerInterface $manager,$id,Specialite $specialite){ 
        $repo=$this->getDoctrine()->getRepository(Specialite::class);
        $id=$repo->find($id);      
        $manager->remove($specialite);
        $manager->flush();
        return $this->redirectToRoute('showSpecialite'); 
}
}

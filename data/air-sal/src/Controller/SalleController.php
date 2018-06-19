<?php

namespace App\Controller;

use App\Entity\Salle as Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use App\Entity\Prestations as Prestations;
use App\Form\PrestationsType;
use App\Repository\PrestationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/salle")
 */
class SalleController extends Controller
{
    /**
     * @Route("/", name="salle_index", methods="GET")
     */
    public function index(SalleRepository $salleRepository): Response
    {
        return $this->render('salle/index.html.twig', ['salles' => $salleRepository->findAll()]);
    }

    /**
     * @Route("/new", name="salle_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->persist($salle);
            $em->flush();

            return $this->redirectToRoute('salle_index');
        }

        $sql = "select id, name, price_surface, price_user, price_fixed from prestations";
        $pdo = $em->getConnection()->prepare($sql);
        $pdo->execute();
        $prestations = $pdo->fetchAll();
        
        return $this->render('salle/new.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
            'prestations' => $prestations
        ]);
    }

    /**
     * @Route("/{id}", name="salle_show", methods="GET")
     */
    public function show(Salle $salle): Response
    {
        return $this->render('salle/show.html.twig', ['salle' => $salle]);
    }

    /**
     * @Route("/{id}/edit", name="salle_edit", methods="GET|POST")
     */
    public function edit(Request $request, Salle $salle): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('salle_edit', ['id' => $salle->getId()]);
        }

        return $this->render('salle/edit.html.twig', [
            'salle' => $salle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="salle_delete", methods="DELETE")
     */
    public function delete(Request $request, Salle $salle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salle->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($salle);
            $em->flush();
        }

        return $this->redirectToRoute('salle_index');
    }
}

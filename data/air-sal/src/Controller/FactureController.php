<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Reservation;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/facture")
 */
class FactureController extends Controller
{
    /**
     * @Route("/", name="facture_index", methods="GET")
     */
    public function index(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/index.html.twig', ['factures' => $factureRepository->findAll()]);
    }

    /**
     * @Route("/{id}/new", name="facture_new", methods="GET|POST")
     */
    public function new(Request $request, Reservation $reservation): Response
    {
        //die(var_dump($reservation));
        $facture = new Facture();
        $em = $this->getDoctrine()->getManager();
        //$reservation = $request->request->get('reservation');

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonReservation = $serializer->serialize($reservation, 'json');
        die(var_dump($jsonReservation));
        $facture->setData($jsonReservation);
        $facture->setReservation($reservation);
        $facture->setPrice($reservation->getReservationPrice());

        $em->persist($facture);
        $em->flush();
       
        return $this->redirectToRoute('facture_show', ['id' => $facture->getId()]);
    }

    /**
     * @Route("/{id}", name="facture_show", methods="GET")
     */
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', ['facture' => $facture]);
    }

    /**
     * @Route("/{id}/edit", name="facture_edit", methods="GET|POST")
     */
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('facture_edit', ['id' => $facture->getId()]);
        }

        return $this->render('facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="facture_delete", methods="DELETE")
     */
    public function delete(Request $request, Facture $facture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($facture);
            $em->flush();
        }

        return $this->redirectToRoute('facture_index');
    }
}

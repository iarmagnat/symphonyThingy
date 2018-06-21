<?php

namespace App\Controller;
use \stdClass;
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
use Symfony\Component\Serializer\Annotation\MaxDepth;


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
        /*die(var_dump($reservation));
        
        //$reservation = $request->request->get('reservation');

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = new ObjectNormalizer();
        $normalizers->setMaxDepthHandler(function($element){
            return $element->getId();
        });
        $serializer = new Serializer(array($normalizers), $encoders);
        
        $jsonReservation = $serializer->normalize($reservation, null, array('enable_max_depth' => true));
       // $jsonReservation = $serializer->serialize($jsonReservation, 'json');
        die(var_dump($jsonReservation));*/

        $facture = new Facture();
        $em = $this->getDoctrine()->getManager();
        $newFacture = $this->reservationToJson($reservation);
        $facture->setData(json_encode($newFacture));
        $facture->setReservation($reservation);
        $facture->setPrice($reservation->getReservationPrice());
        
        $em->persist($facture);
        $reservation->setFacture($facture);
        $em->persist($reservation);
        $em->flush();
        return $this->redirectToRoute('facture_show', ['id' => $facture->getId()]);
    }

    private function reservationToJson(Reservation $reservation)
    {
        $f = new \stdClass;
        $u = new \stdClass;
        $s = new \stdClass;
        $r = new \stdClass;

        //la salle
        $s -> name = $reservation->getSalle()->getName() ;
        $s -> size = $reservation->getSalle()->getSize();
        $s -> capacity = $reservation->getSalle()->getCapacity();
        $s -> address = $reservation->getSalle()->getAddress();
        $s -> price = $reservation->getSalle()->getPrice();

        $u -> name = $reservation->getUser()->getUsername();
        $u -> email = $reservation->getUser()->getEmail();      
        
        $r -> DateStart = $reservation->getDateStart();
        $r -> DateEnd = $reservation->getDateEnd();
        $r -> prix = $reservation->getReservationPrice();
        

        $prestarions = array();
        foreach($reservation->getPrestations() as $presta){
            $p = new \stdClass;
            $p->price_fixed = $presta->getPriceFixed();
            $p->price_user = $presta->getPriceUser();
            $p->price_surface = $presta->getPriceSurface();            
            $prestations[$presta->getName()] = $p;
        }

        $f -> user = $u;
        $f -> salle = $s;
        $f -> reservation = $r;
        $f -> prestations = $prestations;

        return $f;

    }

    /**
     * @Route("/{id}", name="facture_show", methods="GET")
     */
    public function show(Facture $facture ): Response
    {
        $data = json_decode($facture->getData(), true);        
        return $this->render('facture/show.html.twig', ['facture' => $facture, "data"=>$data]);
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

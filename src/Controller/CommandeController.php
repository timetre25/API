<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commande;
use App\Entity\Terrain;
use App\Repository\ArticleRepository;
use App\Repository\AvantProjetRepository;
use App\Repository\CommandeRepository;
use App\Repository\StatutCommandeRepository;
use App\Repository\TvaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommandeController extends AbstractController{
    private CommandeRepository $commandeRepository;
    private SerializerInterface $serializer;
    private StatutCommandeRepository $statutCommandeRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(CommandeRepository $commandeRepository,
                                SerializerInterface $serializer,
                                StatutCommandeRepository $statutCommandeRepository,
                                EntityManagerInterface $entityManager,
                                ValidatorInterface $validator)
    {
        $this->commandeRepository = $commandeRepository;
        $this->serializer = $serializer;
        $this->statutCommandeRepository = $statutCommandeRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;

    }
    /**
     * @Route("/api/getCommandes", name="get_commandes", methods={"GET"})
     */
    public function getCommandes(){
        $commandes = $this->commandeRepository->findAll();
        $commandesJson = $this->serializer->serialize($commandes,'json');
        return new JsonResponse($commandesJson,Response::HTTP_OK,[],true);
    }





    /**
     * @Route("/api/commande/{id}", name="api_commande_updateCommande", methods={"PATCH"})
     */
    public function updateCommande($id, Request $request): Response
    {
        $commande = $this->commandeRepository->find($id);
        $info = json_decode($request->getContent(),true);
        $statut = $this->statutCommandeRepository->find($info['idStatut']);
        $commande->setIdStatut($statut);
        $this->entityManager->merge($commande); // Préparer l'ordre INSERT
        $this->entityManager->flush(); // Envoyer l'ordre INSERT vers la base de données
        $commandeJson = $this->serializer->serialize($commande,'json');
        return new JsonResponse($commandeJson,Response::HTTP_OK,[] ,true);
    }


}
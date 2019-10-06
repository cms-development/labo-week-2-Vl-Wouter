<?php


namespace App\Controller;


use App\Entity\Camp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    /**
     * @Route("/camp/{slug}/like/{status}", name="likeCamp", methods={"GET"})
     * @param $status
     * @param $slug
     * @return JsonResponse
     */
    public function save($status, $slug) {
        $manager = $this->getDoctrine()->getManager();
        $camp = $this->getDoctrine()->getRepository(Camp::class)->findOneBy(["slug" => $slug]);
        if($status == true) {
            $likes = $camp->getLikes() + 1;
        } else {
            $likes = $camp->getLikes() - 1;
        }
        $camp->setLikes($likes);

        $manager->persist($camp);
        $manager->flush();

        return $this->json(['status' => $status]);
    }
}
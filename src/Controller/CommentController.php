<?php


namespace App\Controller;


use App\Entity\Camp;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/new/{camp_id}", name="addComment", methods={"POST"})
     * @param $camp_id
     * @return RedirectResponse
     */
    public function add($camp_id) {
        $camp = $this->getDoctrine()->getRepository(Camp::class)->find($camp_id);

        $manager = $this->getDoctrine()->getManager();

        $comment = new Comment();
        $comment->setAuthor($_POST['author']);
        $comment->setContent($_POST['content']);
        $comment->setCampId($camp);

        $manager->persist($comment);
        $manager->flush();

        return $this->redirectToRoute('camp', ['slug' => $camp->getSlug()]);
    }
}
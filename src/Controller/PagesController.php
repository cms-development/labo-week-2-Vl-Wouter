<?php


namespace App\Controller;


use App\Entity\Camp;
use App\Entity\Comment;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    /**
     * @Route("/", name="start")
     * @param Request $r
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function start(Request $r) {
        $locale = $r->getLocale();
        if($locale !== "nl" || $locale !== "en") {
            $locale = $r->getDefaultLocale();
        }
        return $this->redirectToRoute("index", ["_locale" => $locale]);
    }

    /**
     * @Route(
     *     "/{_locale}/",
     *     locale="nl",
     *     requirements={
     *          "_locale": "nl|en"
     *     },
     *     name="index"
     * )
     * @param Request $r
     * @return Response
     */
    public function index(Request $r) {
        $locale = $r->getLocale();
        $camps = $this->getDoctrine()->getRepository(Camp::class)->findBy(array(), array('id' => 'desc'), 4);
        $featured = $this->getDoctrine()->getRepository(Camp::class)->findBy(["featured" => 1]);
        shuffle($featured);

        if(!$camps) {
            throw $this->createNotFoundException('No camps found.');
        }

        if(!$featured) {
            throw $this->createNotFoundException('No featured camps available');
        }

        return $this->render('pages/index.html.twig', ['locale' => $locale, 'camps' => $camps, 'featured' => $featured[0]]);
    }

    /**
     * @param $id
     * @param Request $r
     * @return Response
     * @Route("/{_locale}/camp/{id}", locale="nl", requirements={"_locale": "nl|en"}, name="camp", methods={"GET", "POST"})
     */
    public function show($id, Request $r) {
        $locale = $r->getLocale();
        $camp = $this->getDoctrine()->getRepository(Camp::class)->find($id);

        $comment = new Comment();

        $comment_form = $this->createFormBuilder($comment)
                            ->add('author', TextType::class)
                            ->add('content', TextareaType::class)
                            ->add('save', SubmitType::class)
                            ->getForm();

        $comment_form->handleRequest($r);

        if($comment_form->isSubmitted() && $comment_form->isValid()) {
            $comment = $comment_form->getData();
            $comment->setCamp($camp);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('camp', ['_locale' => $locale, 'id' => $camp->getId()]);
        }

        return $this->render('pages/camp.html.twig', [
            'locale' => $locale,
            'camp' => $camp,
            'comment_form' => $comment_form->createView(),
        ]);
    }
}
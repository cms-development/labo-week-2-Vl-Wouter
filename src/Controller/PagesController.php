<?php


namespace App\Controller;


use App\Entity\Camp;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response
     */
    public function index() {
        $camps = $this->getDoctrine()->getRepository(Camp::class)->findBy(array(), array('id' => 'desc'), 4);
        $featured = $this->getDoctrine()->getRepository(Camp::class)->findBy(["featured" => 1]);
        shuffle($featured);

        if(!$camps) {
            throw $this->createNotFoundException('No camps found.');
        }

        if(!$featured) {
            throw $this->createNotFoundException('No featured camps available');
        }

        return $this->render('pages/index.html.twig', ['camps' => $camps, 'featured' => $featured[0]]);
    }

    /**
     * @param string $slug
     * @Route("/camp/{slug}", name="camp", methods={"GET"})
     * @return Response
     */
    public function show($slug) {
        $camp = $this->getDoctrine()->getRepository(Camp::class)->findOneBy(["slug" => $slug]);

        return $this->render('pages/camp.html.twig', ['camp' => $camp]);
    }

    /**
     * @Route("/admin/camps/new", name="newCamp", methods={"GET", "POST"})
     * @param Request $r
     * @return Response
     * @throws Exception
     */
    public function addCamp(Request $r) {
        if($r->isMethod('GET')) {
            return $this->render('cms/campForm.html.twig');
        } else if($r->isMethod('POST')) {
            $manager = $this->getDoctrine()->getManager();

            $camp = new Camp();
            $camp->setTitle($_POST["title"]);
            $camp->setAuthor("TestGebruiker");
            $camp->setQuote($_POST["quote"]);
            $camp->setDate(new DateTime($_POST["date"]));
            $camp->setImage($_POST["image"]);
            $camp->setDescription($_POST["description"]);
            $camp->setLikes(0);
            if(array_key_exists("featured", $_POST)) {
                $camp->setFeatured($_POST["featured"]);
            }

            $manager->persist($camp);

            $manager->flush();

            return $this->redirectToRoute('index', [], 301);

        }
    }
}
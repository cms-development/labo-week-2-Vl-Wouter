<?php

namespace App\Controller;

use App\Entity\Camp;
use App\Entity\CampTranslation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampController extends AbstractController
{
    /**
     * @Route("/admin", name="cmsIndex")
     */
    public function index()
    {
        $camps = $this->getDoctrine()->getRepository(Camp::class)->findAll();
        return $this->render('camp/index.html.twig', [
            'camps' => $camps,
        ]);
    }

    /**
     * @Route("/camp/new", name="newCamp", methods={"GET", "POST"})
     * @param Request $r
     * @return Response
     * @throws \Exception
     */
    public function addCamp(Request $r) {
        $camp = new Camp();

        $form= $this->createFormBuilder($camp)
                    ->add('author', TextType::class)
                    ->add('title', TextType::class)
                    ->add('date', DateType::class)
                    ->add('featured', CheckboxType::class, [
                        'required' => false,
                        'label' => 'In de kijker',
                        'empty_data' => false
                    ])
                    ->add('image', TextType::class)
                    ->getForm();

        $form_nl = $this->createFormBuilder(null)
                        ->add('nl_description', TextareaType::class)
                        ->add('nl_quote', TextType::class)
                        ->getForm();

        $form_en = $this->createFormBuilder(null)
                        ->add('en_description', TextareaType::class)
                        ->add('en_quote', TextType::class)
                        ->add('save', SubmitType::class, [
                            'label' => "Voeg kamp toe"
                        ])
                        ->getForm();

        if($r->isMethod('GET')) {
            return $this->render('camp/new.html.twig', [
                'form' => $form->createView(),
                'form_nl' => $form_nl->createView(),
                'form_en' => $form_en->createView(),
            ]);
        } else if($r->isMethod('POST')) {
            $form_data = $r->request->get('form');
            $date_string = implode("-", $form_data['date']);
            //dump($form_data);
            //die();
            $manager = $this->getDoctrine()->getManager();
            // Probably not efficient but it works
            // adding properties to camp
            $camp->setTitle($form_data['title']);
            $camp->setAuthor($form_data['author']);
            $camp->setDate(new \DateTime($date_string));
            $camp->setImage($form_data['image']);
            $camp->setLikes(0);
            if(array_key_exists('featured', $form_data)) {
                $camp->setFeatured($form_data['featured']);
            }

            // Set translatables
            $camp->translate('nl')->setDescription($form_data['nl_description']);
            $camp->translate('nl')->setQuote($form_data['nl_quote']);
            $camp->translate('en')->setDescription($form_data['en_description']);
            $camp->translate('en')->setQuote($form_data['en_quote']);

            // Merge new translations
            $camp->mergeNewTranslations();

            // Push to DB
            $manager->persist($camp);
            $manager->flush();

            // Redirect
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/admin/camp/{id}/remove", name="removeCamp")
     * @param Camp $camp
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Camp $camp) {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($camp);
        $manager->flush();
        return $this->redirectToRoute('cmsIndex');
    }

    /**
     * @Route("/{_locale}/camp/{id}/like", locale="nl", requirements={"_locale": "nl|en"} , name="likeCamp", methods={"POST"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function like($id, Request $r) {
        $camp = $this->getDoctrine()->getRepository(Camp::class)->find($id);
        $data = json_decode($r->getContent(), true);
        if($data['liked'] === true) {
            $newLikes = $camp->getLikes() + 1;
        } else {
            $newLikes = $camp->getLikes() - 1;
        }
        $camp->setLikes($newLikes);

        $manager = $this->getDoctrine()->getManager();

        $manager->persist($camp);
        $manager->flush();

        return $this->json(['liked' => $data['liked'], 'likes' => $newLikes]);
    }
}

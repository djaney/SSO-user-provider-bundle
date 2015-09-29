<?php

namespace Arcanys\SSOAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ArcanysSSOAuthBundle:Default:index.html.twig', array('name' => $name));
    }
}

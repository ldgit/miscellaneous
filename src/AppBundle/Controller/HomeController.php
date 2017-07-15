<?php 

namespace Events\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        return new JsonResponse($this->renderView('home/home.json.twig', []));
    }
}

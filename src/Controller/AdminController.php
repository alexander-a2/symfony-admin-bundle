<?php

namespace AlexanderA2\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route("", name: "index")]
    public function indexAction(): Response
    {
        return $this->render('@Admin/layout/dashboard.html.twig');
    }
}

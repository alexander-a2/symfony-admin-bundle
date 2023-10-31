<?php

namespace AlexanderA2\SymfonyAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route("admin/", name: "admin_")]
class AdminController extends AbstractController
{
    #[Route("", name: "index")]
    public function indexAction(): Response
    {
        return $this->render('@Admin/index.html.twig');
    }
}
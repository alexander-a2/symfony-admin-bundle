<?php

namespace AlexanderA2\SymfonyAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("development/", name: "development_")]
class DevelopmentController extends AbstractController
{
    #[Route("", name: "index")]
    public function indexAction(): Response
    {
        return $this->render('@Admin/development/index.html.twig');
    }

    #[Route("views/templates-and-layouts", name: "views_templates_and_layouts")]
    public function availableComponentsAction(): Response
    {
        return $this->render('@Admin/development/templates_and_layouts.html.twig');
    }

    #[Route("views/bootstrap-cheatsheet", name: "views_bootstrap_cheatsheet")]
    public function bootstrapCheatsheetAction(): Response
    {
        return $this->render('@Admin/development/bootstrap_cheatsheet.html.twig');
    }
}
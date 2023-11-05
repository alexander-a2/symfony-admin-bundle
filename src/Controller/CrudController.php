<?php

namespace AlexanderA2\SymfonyAdminBundle\Controller;

use AlexanderA2\SymfonyAdminBundle\Builder\EntityDataBuilder;
use AlexanderA2\SymfonyAdminBundle\Builder\FormBuilder;
use AlexanderA2\SymfonyAdminBundle\Builder\EntityDatasheetBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[IsGranted('ROLE_ADMIN')]
#[Route("admin/crud/", name: "admin_crud_")]
class CrudController extends AbstractController
{
    #[Route("index", name: "index")]
    public function indexAction(
        Request                $request,
        EntityDatasheetBuilder $entityDatasheetBuilder,
    ): Response {
        $entityClassName = $request->get('entityClassName');

        return $this->render('@Admin/crud/index.html.twig', [
            'entityDatasheet' => $entityDatasheetBuilder->build($entityClassName),
            'entityClassName' => $entityClassName,
        ]);
    }

    #[Route("view", name: "view")]
    public function viewAction(
        Request                $request,
        EntityManagerInterface $entityManager,
        EntityDataBuilder      $entityDataBuilder,
    ): Response {
        $entityClassName = $request->get('entityClassName');
        $entityId = $request->get('entityId');
        $entity = $entityManager->getRepository($entityClassName)->find($entityId);

        return $this->render('@Admin/crud/view.html.twig', [
            'entity' => $entity,
            'data' => $entityDataBuilder->getData($entity),
            'entityClassName' => $entityClassName,
            'entityId' => $entityId,
        ]);
    }

    #[Route("edit", name: "edit")]
    public function editAction(
        Request                $request,
        EntityManagerInterface $entityManager,
        FormBuilder            $formBuilder,
    ): Response {
        $entityClassName = $request->query->get('entityClassName');
        $entityId = $request->query->get('entityId');

        if ($entityId) {
            $entity = $entityManager->getRepository($entityClassName)->find($entityId);
        } else {
            $entity = new $entityClassName;
        }

        $form = $formBuilder->buildFor($entity);
        $form->setData($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$entityId) {
                    $entityManager->persist($entity);
                }
                $entityManager->flush();
                $this->addFlash('success', $entityId ? 'admin.entity.crud.record_was_updated' : 'admin.entity.crud.record_was_created');
            } catch (Throwable $exception) {
                $this->addFlash('error', 'admin.something_went_wrong');
            }

            return $this->redirectToRoute('admin_crud_view', [
                'entityClassName' => $entityClassName,
                'entityId' => $entity->getId(),
            ]);
        }

        return $this->render('@Admin/crud/edit.html.twig', [
            'form' => $form,
            'entityClassName' => $entityClassName,
            'entityId' => $entityId ?? null,
        ]);
    }

    #[Route("delete", name: "delete")]
    public function deleteAction(
        Request                $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $entityClassName = $request->query->get('entityClassName');
        $entityId = $request->query->get('entityId');
        $entity = $entityManager->getRepository($entityClassName)->find($entityId);

        try {
            $entityManager->remove($entity);
            $entityManager->flush();
            $this->addFlash('success', 'admin.entity.crud.record_was_deleted');
        } catch (Throwable $exception) {
            $this->addFlash('error', 'admin.something_went_wrong');
        }

        return $this->redirectToRoute('admin_crud_index', [
            'entityClassName' => $entityClassName,
        ]);
    }
}
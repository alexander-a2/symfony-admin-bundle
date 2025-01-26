<?php

namespace AlexanderA2\AdminBundle\Controller;

use AlexanderA2\AdminBundle\Datasheet\Builder\DatasheetBuilder;
use AlexanderA2\AdminBundle\Helper\StringHelper;
use AlexanderA2\AdminBundle\Builder\EntityDatasheetBuilder;
use AlexanderA2\AdminBundle\Builder\EntityFormBuilder;
use AlexanderA2\AdminBundle\Builder\MenuBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

#[Route("crud/", name: "crud_")]
class CrudController extends AbstractController
{
    #[Route("list", name: "list")]
    public function indexAction(
        Request $request,
        EntityDatasheetBuilder $entityDatasheetBuilder,
    ): Response {
        $entityFqcn = $request->get('entityFqcn');
        $datasheet = $entityDatasheetBuilder->buildDatasheet($entityFqcn);
//        $datasheet->setDebug(true);
        $datasheet->setQueryStringParameters([
            'entityFqcn' => $entityFqcn,
        ]);

        return $this->render('@Admin/entity/list.html.twig', [
            'datasheet' => $datasheet,
            'entity' => $entityFqcn,
        ]);
    }

    #[Route("view", name: "view")]
    public function viewAction(
        Request $request,
        EntityDatasheetBuilder $entityDatasheetBuilder,
        DatasheetBuilder $datasheetBuilder,
    ): Response {
        $entityFqcn = $request->get('entityFqcn');
        $entityId = $request->get('entityId');
        $datasheet = $entityDatasheetBuilder->buildDatasheet($entityFqcn, $entityId);
        $datasheetBuilder->build($datasheet);

        return $this->render('@Admin/entity/view.html.twig', [
            'columns' => $datasheet->getColumns(),
            'record' => $datasheet->getData()[0],
            'entity' => [$entityFqcn, $entityId],
        ]);
    }

    #[Route("create", name: "create")]
    public function createAction(
        Request $request,
        EntityManagerInterface $entityManager,
        EntityFormBuilder $formBuilder,
        TranslatorInterface $translator,
    ): Response {
        return $this->editAction(
            $request,
            $entityManager,
            $formBuilder,
            $translator,
        );
    }

    #[Route("edit", name: "edit")]
    public function editAction(
        Request $request,
        EntityManagerInterface $entityManager,
        EntityFormBuilder $formBuilder,
    ): Response {
        $entityFqcn = $request->query->get('entityFqcn');
        $entityId = $request->query->get('entityId');

        if ($entityId) {
            $entity = $entityManager->getRepository($entityFqcn)->find($entityId);
        } else {
            $entity = new $entityFqcn;
        }
        $form = $formBuilder->get($entity);
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
                'entityFqcn' => $entityFqcn,
                'entityId' => $entity->getId(),
            ]);
        }

        return $this->render('@Admin/entity/edit.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }


    #[Route("delete", name: "delete")]
    public function deleteAction(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $entityFqcn = $request->query->get('entityFqcn');
        $entityId = $request->query->get('entityId');
        $entity = $entityManager->getRepository($entityFqcn)->find($entityId);
        $entityManager->remove($entity);
        $entityManager->flush();

        return $this->redirectToRoute('admin_crud_list', [
            'entityFqcn' => $entityFqcn,
        ]);
    }
}

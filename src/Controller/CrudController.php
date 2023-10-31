<?php

namespace AlexanderA2\SymfonyAdminBundle\Controller;

use AlexanderA2\PhpDatasheet\Datasheet;
use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\SymfonyAdminBundle\Builder\EntityDataBuilder;
use AlexanderA2\SymfonyAdminBundle\Builder\FormBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[IsGranted('ROLE_ADMIN')]
#[Route("admin/crud/", name: "admin_crud_")]
class CrudController extends AbstractController
{
    #[Route("index", name: "index")]
    public function indexAction(
        Request                $request,
        EntityManagerInterface $entityManager,
        RouterInterface        $router,
    ): Response {
        $objectClassName = $request->get('objectClassName');
        $objectListDatasheet = new Datasheet(
            $entityManager->getRepository($objectClassName)->createQueryBuilder('o'),
        );

        $primaryFieldName = EntityHelper::guessPrimaryFieldName(
            EntityHelper::getEntityFields($objectClassName, $entityManager),
        );

        if ($primaryFieldName) {
            $objectListDatasheet
                ->getColumn($primaryFieldName)
                ->setHandler(function ($value, $record) use ($objectClassName, $router) {
                    return sprintf(
                        '<b><a href="%s">%s</a></b>',
                        $router->generate('admin_crud_view', [
                            'objectClassName' => $objectClassName,
                            'objectId' => $record['id'],
                        ]),
                        $value,
                    );
                });
        }

        return $this->render('@Admin/crud/index.html.twig', [
            'objectListDatasheet' => $objectListDatasheet,
            'objectClassName' => $objectClassName,
        ]);
    }

    #[Route("view", name: "view")]
    public function viewAction(
        Request                $request,
        EntityManagerInterface $entityManager,
        EntityDataBuilder      $entityDataBuilder,
    ): Response {
        $objectClassName = $request->get('objectClassName');
        $objectId = $request->get('objectId');
        $object = $entityManager->getRepository($objectClassName)->find($objectId);

        return $this->render('@Admin/crud/view.html.twig', [
            'object' => $object,
            'data' => $entityDataBuilder->getData($object),
            'objectClassName' => $objectClassName,
            'objectId' => $objectId,
        ]);
    }

    #[Route("edit", name: "edit")]
    public function editAction(
        Request                $request,
        EntityManagerInterface $entityManager,
        FormBuilder            $formBuilder,
    ): Response {
        $objectClassName = $request->query->get('objectClassName');
        $objectId = $request->query->get('objectId');

        if ($objectId) {
            $object = $entityManager->getRepository($objectClassName)->find($objectId);
        } else {
            $object = new $objectClassName;
        }

        $form = $formBuilder->buildFor($object);
        $form->setData($object);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$objectId) {
                    $entityManager->persist($object);
                }
                $entityManager->flush();
                $this->addFlash('success', $objectId ? 'Record was updated' : 'Record was created');
            } catch (Throwable $exception) {
                $this->addFlash('error', 'Failed to update the record');
            }

            return $this->redirectToRoute('admin_crud_view', [
                'objectClassName' => $objectClassName,
                'objectId' => $object->getId(),
            ]);
        }

        return $this->render('@Admin/crud/edit.html.twig', [
            'form' => $form,
            'objectClassName' => $objectClassName,
            'objectId' => $objectId ?? null,
        ]);
    }

    #[Route("delete", name: "delete")]
    public function deleteAction(
        Request                $request,
        EntityManagerInterface $entityManager,
        FormBuilder            $formBuilder,
    ): Response {
        $objectClassName = $request->query->get('objectClassName');
        $objectId = $request->query->get('objectId');
        $object = $entityManager->getRepository($objectClassName)->find($objectId);

        try {
            $entityManager->remove($object);
            $entityManager->flush();
            $this->addFlash('success', 'Record was deleted');
        } catch (Throwable $exception) {
            $this->addFlash('error', 'Failed to delete the record');
        }

        return $this->redirectToRoute('admin_crud_index', [
            'objectClassName' => $objectClassName,
        ]);
    }
}
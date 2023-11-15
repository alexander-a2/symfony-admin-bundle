<?php

namespace AlexanderA2\SymfonyAdminBundle\EventSubscriber;

use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\SymfonyAdminBundle\Event\EntityDatasheetBuildEvent;
use AlexanderA2\SymfonyAdminBundle\Event\EntityFormBuildEvent;
use App\Entity\Employee;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class NestedTreeEntityDatasheetSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected EntityHelper    $entityHelper,
        protected RouterInterface $router,
    ) {
    }

    public function updateDatasheet(EntityDatasheetBuildEvent $event): void
    {
        if (!$event->getDatasheet()->getSource() instanceof NestedTreeRepository) {
            return;
        }
        $entityClassName = $event->getEntityClassName();
        $primaryFieldName = EntityHelper::guessPrimaryFieldName(
            $this->entityHelper->getEntityFields($entityClassName),
        );

        if (!$primaryFieldName) {
            return;
        }
        /** @var NestedTreeRepository $repository */
        $datasheet = $event->getDatasheet();
        $router = $this->router;
        $event
            ->getDatasheet()
            ->getColumn($primaryFieldName)
            ->setHandler(function ($value, $entity) use ($entityClassName, $router, $datasheet) {
                return sprintf(
                    '<b><a href="%s">%s</a></b>',
                    $router->generate('admin_crud_view', [
                        'entityClassName' => $entityClassName,
                        'entityId' => $entity->getId(),
                    ]),
                    $datasheet->getDataReader()->getRepository()->getPathAsString($entity, [
                        'separator' => ' &rarr; ',
                    ]),
                );
            });
    }

    public function updateForm(EntityFormBuildEvent $event): void
    {
        if ($event->getEntityClassName() != Employee::class) {
            return;
        }
        $event->getFormBuilder()->remove('joinedAt');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityDatasheetBuildEvent::class => 'updateDatasheet',
//            EntityFormBuildEvent::class => 'updateForm',
        ];
    }
}
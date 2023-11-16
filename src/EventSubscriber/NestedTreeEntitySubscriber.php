<?php

namespace AlexanderA2\SymfonyAdminBundle\EventSubscriber;

use AlexanderA2\PhpDatasheet\Helper\EntityHelper;
use AlexanderA2\PhpDatasheet\Helper\StringHelper;
use AlexanderA2\SymfonyAdminBundle\Event\EntityDataBuildEvent;
use AlexanderA2\SymfonyAdminBundle\Event\EntityDatasheetBuildEvent;
use AlexanderA2\SymfonyAdminBundle\Event\EntityFormBuildEvent;
use AlexanderA2\SymfonyDatasheetBundle\Builder\Column\NestedTreeDatasheetColumnBuilder;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class NestedTreeEntitySubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected EntityHelper           $entityHelper,
        protected RouterInterface        $router,
        protected EntityManagerInterface $entityManager,
    ) {
    }

    public function onDatasheetBuild(EntityDatasheetBuildEvent $event): void
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

    public function onFormBuild(EntityFormBuildEvent $event): void
    {
        if (!$this->isEntityOfNestedTree($event->getEntityClassName())) {
            return;
        }

        foreach(array_merge(NestedTreeDatasheetColumnBuilder::ENTITY_SPECIFIC_FIELDS, ['root']) as $fieldName){
            $event->getFormBuilder()->remove($fieldName);
        }
//        $event->getFormBuilder()->add('parent')->getForm()->getConfig()->setOption('attr', ['label' => 'asd']);
    }

    public function onDataBuild(EntityDataBuildEvent $event): void
    {
        if (!$this->isEntityOfNestedTree(get_class($event->getSubject()))) {
            return;
        }
        $data = $event->getData();

        foreach ($data as $index => $item) {
            if (in_array(StringHelper::toSnakeCase($item['name']), NestedTreeDatasheetColumnBuilder::ENTITY_SPECIFIC_FIELDS)) {
                unset($data[$index]);
            }
        }
        $event->setData($data);
    }

    protected function isEntityOfNestedTree(string $entityClassName): bool
    {
        return $this->entityManager->getRepository($entityClassName) instanceof NestedTreeRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityDatasheetBuildEvent::class => 'onDatasheetBuild',
            EntityDataBuildEvent::class => 'onDataBuild',
            EntityFormBuildEvent::class => 'onFormBuild',
        ];
    }
}
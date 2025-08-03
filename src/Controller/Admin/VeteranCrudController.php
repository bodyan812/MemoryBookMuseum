<?php

namespace App\Controller\Admin;

use App\Entity\Veteran;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class VeteranCrudController extends AbstractCrudController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getEntityFqcn(): string
    {
        return Veteran::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Ветеран')
            ->setEntityLabelInPlural('Ветераны')
            ->setSearchFields(['lastName', 'firstName', 'middleName'])
            ->setDefaultSort(['lastName' => 'ASC'])
            ->setFormOptions(['attr' => ['enctype' => 'multipart/form-data']])
            ->showEntityActionsInlined();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $request = $this->requestStack->getCurrentRequest();
        $session = $this->requestStack->getSession();

        $warType = $request->query->get('warType');

        if (!$warType) {
            $warType = $session->get('current_war_type');
        }

        if (!$warType) {
            $warType = array_values(Veteran::WAR_TYPES)[0];
        }

        $session->set('current_war_type', $warType);

        if ($warType && in_array($warType, array_values(Veteran::WAR_TYPES))) {
            $qb->andWhere('entity.warType = :warType')
                ->setParameter('warType', $warType);
        }

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            TextField::new('lastName', 'Фамилия'),
            TextField::new('firstName', 'Имя'),
            TextField::new('middleName', 'Отчество'),

            ImageField::new('photo', 'Фото')
                ->setBasePath('uploads/photos')
                ->setUploadDir('public/uploads/photos')
                // Отображаем как миниатюру в списке
                ->setTemplatePath('admin/field/thumbnail.html.twig'),

            AssociationField::new('awards', 'Награды')
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getAwards()->map(fn($award) => $award->getTitle())->toArray());
                })
                ->setFormTypeOption('by_reference', false)
                ->autocomplete(),

            AssociationField::new('rank', 'Звание')
                ->formatValue(function ($value, $entity) {
                    return $entity->getRank() ? $entity->getRank()->getTitle() : '';
                }),

            DateField::new('birthDate', 'Дата рождения'),
            DateField::new('deathDate', 'Дата смерти')
                ->hideOnIndex(),

            CollectionField::new('media', 'Медиа')
                ->useEntryCrudForm()
                ->setTemplatePath('admin/field/media_collection.html.twig')
                ->hideOnIndex(),
        ];

        // Добавляем поле для отображения типа войны только в формах
        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            $fields[] = TextField::new('warTypeLabel', 'Тип войны')
                ->setFormTypeOption('disabled', true);
        }

        return $fields;
    }

    public function createEntity(string $entityFqcn)
    {
        $veteran = new Veteran();
        $session = $this->requestStack->getSession();
        $warType = $session->get('current_war_type');

        // Устанавливаем войну из сессии
        if ($warType && in_array($warType, array_values(Veteran::WAR_TYPES))) {
            $veteran->setWarType($warType);
        } else {
            // Если война не установлена, используем первую доступную
            $warType = array_values(Veteran::WAR_TYPES)[0];
            $veteran->setWarType($warType);
        }

        return $veteran;
    }
}

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
use Vich\UploaderBundle\Form\Type\VichImageType;

class VeteranCrudController extends AbstractCrudController
{
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
            ->showEntityActionsInlined();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Фильтрация по типу войны
        $warType = $this->getContext()->getRequest()->query->get('warType');
        if ($warType) {
            $qb->andWhere('entity.warType = :warType')
                ->setParameter('warType', $warType);
        }

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('lastName', 'Фамилия'),
            TextField::new('firstName', 'Имя'),
            TextField::new('middleName', 'Отчество'),

            ImageField::new('photo', 'Фото')
                ->setBasePath('uploads/photos')
                ->setUploadDir('public/uploads/photos')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false)
                ->hideOnIndex(),

            AssociationField::new('awards', 'Награды')
                ->setFormTypeOption('choice_label', 'title')
                ->setFormTypeOption('by_reference', false)
                ->autocomplete(),

            AssociationField::new('rank', 'Звание')
                ->setFormTypeOption('choice_label', 'title'),

            DateField::new('birthDate', 'Дата рождения'),
            DateField::new('deathDate', 'Дата смерти'),

            CollectionField::new('media', 'Медиа')
                ->useEntryCrudForm()
                ->setTemplatePath('admin/field/media_collection.html.twig')
                ->hideOnIndex(),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $veteran = new Veteran();

        // Установка типа войны из параметра запроса
        $warType = $this->getContext()->getRequest()->query->get('warType');
        if ($warType) {
            $veteran->setWarType($warType);
        }

        return $veteran;
    }
}

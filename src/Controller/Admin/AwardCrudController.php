<?php

namespace App\Controller\Admin;

use App\Entity\Award;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AwardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Award::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('награду')
            ->setEntityLabelInPlural('Награды');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Награда'),
        ];
    }

}

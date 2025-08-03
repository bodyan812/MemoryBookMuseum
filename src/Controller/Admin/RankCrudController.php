<?php

namespace App\Controller\Admin;

use App\Entity\Rank;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RankCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rank::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('звание')
            ->setEntityLabelInPlural('Звания');
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Звание'),
        ];
    }
}

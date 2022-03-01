<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Quizz;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QuizzCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quizz::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            TextField::new('name'),
            AssociationField::new("category")
            ];
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Aircraft;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AircraftCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Aircraft::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Aircraft')
            ->setEntityLabelInPlural('Aircraft')
            ->setSearchFields(['id', 'icao', 'iata', 'modelName', 'title'])
            ->showEntityActionsAsDropdown();
    }

    public function configureFields(string $pageName): iterable
    {
        $icao = TextField::new('icao');
        $iata = TextField::new('iata');
        $modelName = TextField::new('modelName');
        $title = TextField::new('title');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $icao, $iata, $modelName, $title];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $icao, $iata, $modelName, $title];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$icao, $iata, $modelName, $title];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$icao, $iata, $modelName, $title];
        }
    }
}

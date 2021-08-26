<?php

namespace App\Form;


use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchAnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mots', SearchType::class, ['label' => false, 'attr' => ['Class' => 'form-control', 'placeholder' => 'entrez un ou plusieurs mot clé'], 'required'=> false])
            ->add('categorie', EntityType::class, ['label' => false, 'class'=> Categories::class, 'attr' => ['placeholder' => 'entrez un ou plusieurs mot clé'], 'required'=> false])
            ->add('rechercher', SubmitType::class, ['attr'=>['Class'=>"mt-2 btn btn-primary"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

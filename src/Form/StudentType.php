<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        dump($options['data']->getImage());die;
        $avatar="https://images.squarespace-cdn.com/content/54b7b93ce4b0a3e130d5d232/1519987165674-QZAGZHQWHWV8OXFW6KRT/icon.png?content-type=image%2Fpng";
        $builder
            ->add('name')
            ->add('age')
            ->add('adress')
            ->add('image',FileType::class,[
                'label' => 'choisissez votre fichier',
                'data_class' => null,

                'data' => (isset($options['data']) ? $options['data']->getImage() : $avatar),

                'attr' => array(
//                  'class' => 'uploadImage',  // the class already define in form_row
                    'placeholder' => 'Aperçu du médium',
                    'empty_data' => 'remplire le champs',
                )

            ])
            ->add('Save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}

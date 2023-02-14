<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Profile;
use App\Entity\Personne;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child:'firstname')
            ->add(child:'name')
            ->add(child:'age')
            ->add(child:'createdAt')
            ->add(child:'updatedAt')
            ->add(child:'profil', type: EntityType::class, options:[
                'expanded' => true,
                'multiple' => false,
                'class' => Profile::class,
            ])
            ->add(child: 'hobbies', type:EntityType::class, options:[
                'expanded' => false,
                'multiple' => true,
                'class' => Hobby::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('h')
                    ->orderBy('h.designation', 'ASC');
                },
                'choice_label' => 'designation',
            ])
            ->add(child: 'job')
            ->add(child:'photo', type: FileType::class, options: [
                'label' => 'Votre image de  profil (fichiers images uniquement)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])
            ->add(child: 'editer', type: SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
}

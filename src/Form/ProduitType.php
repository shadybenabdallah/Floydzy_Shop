<?php
namespace App\Form;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\File;
class ProduitType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('name')
->add('price', MoneyType::class)
->add('production_date', DateType::class)
->add('made_in', CountryType::class)

->add('photo', FileType::class, [

    'mapped' => false,
    'data_class'=>null,
    'required' => false,
    'data' => $options['photo_value'], // <--- définir la valeur initiale du champ photo
    'constraints' => [
        new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'image/jpeg',
                'image/png',
                'image/gif',
            ],
            'mimeTypesMessage' => 'Please upload a valid JPG, PNG or GIF image',
        ])
    ],
])
;
}
public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Produit::class,
'photo_value' => null,
]);
}
}
<?php
    namespace App\Form;

    use App\Entity\User;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class RegisterType extends AbstractType {
        public function buildForm(FormBuilderInterface $builder, array $options) {
            $builder->add("firstname", TextType::class, [
                "label" => "Prénom",
                "attr" => ["placeholder" => "Votre prénom"]
            ])->add("lastname", TextType::class, [
                "label" => "Nom",
                "attr" => ["placeholder" => "Votre nom"]
            ])->add("birthdate", BirthdayType::class, [
                "label" => "Date de naissance",
                "placeholder" => [
                    "year" => "Année",
                    "month" => "Mois",
                    "day" => "Jour",
                ],
                "years" => range(1940, date("Y")),
                "format" => "dd-MM-yyyy",
            ])
                ->add("pseudo", TextType::class, [
                "label" => "Pseudo",
                "attr" => ["placeholder" => "Votre pseudo"]
            ])->add("email", EmailType::class, [
                "label" => "Adresse email",
                "required" => true,
                "attr" => ["placeholder" => "Votre adresse email"]
            ])->add("password", PasswordType::class, [
                "label" => "Mot de passe",
                "required" => true,
                "attr" => ["placeholder" => "Votre mot de passe"]
            ])->add("save", SubmitType::class, [
                "attr" => ["class" => "save"],
                "label" => "S'Inscrire"
            ]);
        }
        public function configureOptions(OptionsResolver $resolver) {
            $resolver->setDefaults(["data_class" => User::class]);
        }
    };
?>
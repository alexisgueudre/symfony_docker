<?php
    namespace App\Form;

    use App\Entity\User;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;

    class LoginType extends AbstractType {
        public function buildForm(FormBuilderInterface $builder, array $options) {
            $builder->add("email", EmailType::class, [
                "label" => "Adresse email",
                "required" => true,
                "attr" => ["placeholder" => "Votre adresse email"]
            ])->add("password", PasswordType::class, [
                "label" => "Mot de passe",
                "required" => true,
                "attr" => ["placeholder" => "Votre mot de passe"]
            ])->add("save", SubmitType::class, [
                "attr" => ["class" => "save"],
                "label" => "Se Connecter"
            ]);
        }
        public function configureOptions(OptionsResolver $resolver) {
            $resolver->setDefaults(["data_class" => User::class]);
        }
    }
?>
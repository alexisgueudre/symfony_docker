<?php
    namespace App\Form;

    use App\Entity\User;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\EmailType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Component\Security\Core\Security;

    class UpdateType extends AbstractType {
        private $security;
        public function __construct(Security $security) {
            $this->security = $security;
        }
        public function buildForm(FormBuilderInterface $builder, array $options) {
            $user = $this->security->getUser();
            $builder->add("email", EmailType::class, [
                "label" => "Votre nouvelle adresse email",
                "required" => true,
                "attr" => ["value" => $user->getEmail(), "placeholder" => "Votre nouvelle adresse email"]
            ])->add("password", PasswordType::class, [
                "label" => "Votre nouveau mot de passe",
                "required" => true
            ])->add("save", SubmitType::class, [
                "attr" => ["class" => "save"],
                "label" => "Mettre à jour"
            ]);
        }
        public function configureOptions(OptionsResolver $resolver) {
            $resolver->setDefaults(["data_class" => User::class]);
        }
    };
?>
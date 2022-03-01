<?php
    namespace App\Controller;

    use App\Entity\User;
    use App\Entity\Category;
    use App\Entity\Quizz;
    use App\Entity\History;
    use App\Form\RegisterType;
    use App\Form\UpdateType;
    use App\Form\LoginType;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    class UserController extends AbstractController {
        private $session = null;
        private $categories = null;
        private $form = null;
        public function register(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer): Response {
            $this->session = $this->getSession($request);
            $this->setCategories();
            $this->setForm(RegisterType::class);
            $this->form->handleRequest($request);
            if($this->form->isSubmitted() && $this->form->isValid()) {
                $data = $this->form->getData();
                $this->saveUser($data, $encoder);
                $this->sendEmail($data, $mailer);
                return $this->redirectToRoute("confirm");
            };
            return ($this->getUser() != null) ? $this->redirectToRoute("index") : $this->render("user/register.html.twig", ["page" => "Inscription", "categories" => $this->categories, "form" => $this->form->createView()]);
        }
        public function login(Request $request, UserPasswordEncoderInterface $encoder): Response {
            $this->setCategories();
            $this->setForm(LoginType::class);
            return ($this->getUser() != null) ? $this->redirectToRoute("index") : $this->render("user/login.html.twig", ["page" => "Connexion", "categories" => $this->categories, "form" => $this->form->createView()]);
        }
        public function logout($update = false): Response {
            return ($update) ? $this->redirectToRoute("confirm") : $this->redirectToRoute("index");
        }
        public function verify($id): Response {
            $this->setEmailVerified($id);
            return $this->redirectToRoute("login");
        }
        public function confirm(): Response {
            $this->setCategories();
            return $this->render("user/confirm.html.twig", ["page" => "Confirmation", "categories" => $this->categories]);
        }
        public function history(Request $request): Response {
            $this->setCategories();
            $this->session = $this->getSession($request);
            if($this->getUser() == null){
                $histories = $this->session->get('history');
            } else {
                $repository = $this->getDoctrine()->getRepository(History::class);
                $historiesEntry = $repository->findBy(["user" => $this->getUser()->getId()]);
                $quizzsId = [];
                $scores = [];
                $histories = [];
                $dates = [];
                foreach($historiesEntry as $history) {
                    array_push($quizzsId, $history->getIdQuizz()->getId()); 
                    array_push($scores, $history->getScore());
                    array_push($dates, $history->getPlayedAt());
                };
                $repository = $this->getDoctrine()->getRepository(Quizz::class);
                foreach($quizzsId as $key => $quizzId) {
                    $quizz = $repository->findOneById($quizzId);
                    array_push($histories, array("quizz" => $quizz, "score" => $scores[$key], "date" => $dates[$key]));
                };
            };
            return $this->render("user/history.html.twig", ["page" => "Historique", "categories" => $this->categories, 'histories' => $histories]);
        }
        public function update(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer): Response{
            $this->setCategories();
            $this->session = $this->getSession($request);
            if($this->getUser() != null) {
                $this->setForm(UpdateType::class);
                $this->form->handleRequest($request);
                if($this->form->isSubmitted() && $this->form->isValid()) {
                    $data = $this->form->getData();
                    $this->updateUser($data, $encoder);
                    $this->sendEmail(null, $mailer);
                    return $this->redirectToRoute("logout", ["update" => true]);
                };
            };
            return ($this->getUser() != null) ? $this->render("user/update.html.twig", ["page" => "Modifier le profil", "categories" => $this->categories, "form" => $this->form->createView()]) : $this->redirectToRoute("login");
        }
        /* ------------------------------------------------------------------------------------------------- */
        /* Helpers */
        /* ------------------------------------------------------------------------------------------------- */
        public function getSession($request) {
            return $request->getSession();
        }
        public function setCategories() {
            $repository = $this->getDoctrine()->getRepository(Category::class);
            $categories = $repository->findBy([], ["name" => "ASC"]);
            return $this->categories = $categories;
        }
        public function setForm($form) {
            return $this->form = $this->createForm($form);
        }
        public function saveUser($data, $encoder) {
            $password = $encoder->encodePassword(new User, $data->getPassword());
            $data->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            return $entityManager->flush();
        }
        public function sendEmail($data = null, $mailer) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneByEmail((is_null($data)) ? $this->getUser()->getEmail() : $data->getEmail());
            $message = (new \Swift_Message("Vous avez reçu une notification !"))
            ->setFrom("my_admin_quizz@gpotato.com")
            ->setTo((is_null($data)) ? $this->getUser()->getEmail() : $data->getEmail())
            ->setBody($this->renderView("email/email.html.twig", ["id" => (is_null($data)) ? $this->getUser()->getId() : $user->getId()]), "text/html");
            return $mailer->send($message);
        }
        public function setEmailVerified($id) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneById($id);
            $user->setEmailVerifiedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            return $entityManager->flush();
        }
        public function updateUser($data, $encoder) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneByEmail($this->getUser()->getEmail());
            $password = $encoder->encodePassword(new User, $data->getPassword());
            $user->setEmailVerifiedAt(null);
            $user->setEmail($data->getEmail());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            return $entityManager->flush();
        }
    }
?>
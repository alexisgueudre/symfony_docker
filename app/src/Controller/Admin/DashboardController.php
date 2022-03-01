<?php
    namespace App\Controller\Admin;

    use App\Entity\Answer;
    use App\Entity\Category;
    use App\Entity\Question;
    use App\Entity\Quizz;
    use App\Entity\UniqueVisitors;
    use App\Entity\User;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
    use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

    class DashboardController extends AbstractDashboardController {
        /**
         * @IsGranted("ROLE_ADMIN")
         * @Route("/admin", name="admin")
         */
        public function index(): Response {
            $repository = $this->getDoctrine()->getRepository(UniqueVisitors::class);
            $nbr_visitor = $repository->findAll();
            $nbr_visitor = $nbr_visitor[0]->getCurrentValue();
            return $this->render("bundles/EasyAdminBundle/welcome.html.twig", ["nbr_visitor" => $nbr_visitor]);
        }
        public function configureDashboard(): Dashboard {
            return Dashboard::new()->setTitle("My Quizz");
        }
        public function configureMenuItems(): iterable {
            yield MenuItem::linktoDashboard("Dashboard", "fa fa-tachometer");
            yield MenuItem::linkToCrud("Utilisateurs", "fas  fa-users", User::class);
            yield MenuItem::linkToCrud("Catégories", "fas fa-gamepad", Category::class);
            yield MenuItem::linkToCrud("Quizzs", "fas fa-gamepad", Quizz::class);
            yield MenuItem::linkToCrud("Questions", "fas fa-question", Question::class);
            yield MenuItem::linkToCrud("Réponses", "fas fa-tasks", Answer::class);
        }
    }
?>
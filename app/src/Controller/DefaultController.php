<?php
    namespace App\Controller;

    use App\Entity\Category;
    use App\Entity\UniqueVisitors;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Session\Session;

    class DefaultController extends AbstractController {
        private $categories = null;
        public function index(): Response {
            $this->setCategories();

            $this->checkUniqueVisitor();

            return $this->render("pages/home.html.twig", ["page" => "Accueil", "categories" => $this->categories]);
        }
        /* ------------------------------------------------------------------------------------------------- */
        /* Helpers */
        /* ------------------------------------------------------------------------------------------------- */
        public function setCategories() {
            $repository = $this->getDoctrine()->getRepository(Category::class);
            $categories = $repository->findBy([], ["name" => "ASC"]);
            return $this->categories = $categories;
        }
        public function checkUniqueVisitor(){
            $this->session = new Session();

            if($this->session->get('uniqueVisitor') === NULL){
                $this->session->set("uniqueVisitor", true);

                $repository = $this->getDoctrine()->getRepository(UniqueVisitors::class);
                $value = $repository->findOneById(1);

                if($value === NULL){
                    $value = 1;
                }else{
                    $value = intval($value->getCurrentValue()) + 1;
                }

                $entityManager = $this->getDoctrine()->getManager();

                $connection = $entityManager->getConnection();
                $platform   = $connection->getDatabasePlatform();
                $connection->executeUpdate($platform->getTruncateTableSQL('unique_visitors', true));

                $uniqueVisitors = new UniqueVisitors();
                $uniqueVisitors->setCurrentValue($value);

                $entityManager->persist($uniqueVisitors);
                $entityManager->flush();
            }
            return;
        }
    };
?>
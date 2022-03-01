<?php
    namespace App\Controller;

    use App\Entity\Category;
    use App\Entity\Quizz;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class CategoryController extends AbstractController {
        private $categories = null;
        public function index(int $id): Response {
            $this->setCategories();
            $repository = $this->getDoctrine()->getRepository(Quizz::class);
            $quizzs = $repository->findBy(["category" => $id]);
            $repository = $this->getDoctrine()->getRepository(Category::class);
            $category = $repository->findOneById($id);
            return $this->render("category/category.html.twig", ["page" => "Catégorie " . $category->getName(), "category" => $category, "categories" => $this->categories, "quizzs" => $quizzs]);
        }
        public function create(Request $request): Response {
            $this->setCategories();
            $this->addCategory($request);
            return $this->redirectToRoute("index");
        }
        /* ------------------------------------------------------------------------------------------------- */
        /* Helpers */
        /* ------------------------------------------------------------------------------------------------- */
        public function setCategories() {
            $repository = $this->getDoctrine()->getRepository(Category::class);
            $categories = $repository->findBy([], ["name" => "ASC"]);
            return $this->categories = $categories;
        }
        public function addCategory($request) {
            $category = new Category();
            $category->setName($request->query->get("name"));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            return $entityManager->flush();
        }
    }
?>
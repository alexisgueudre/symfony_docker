<?php
    namespace App\Controller;

    use App\Entity\Category;
    use App\Entity\Quizz;
    use App\Entity\Question;
    use App\Entity\Answer;
    use App\Entity\History;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Session\Session;

    class QuizzController extends AbstractController {
        private $categories;
        private $array = [];
        private $score = 0;
        private $feedback = [];
        public function index($id, Request $request): Response {
            static $expected = null;
            $this->setCategories();
            $repository = $this->getDoctrine()->getRepository(Quizz::class);
            $quizz = $repository->findOneById($id);
            $repository = $this->getDoctrine()->getRepository(Question::class);
            $questions = $repository->findBy(["quizz" => $quizz]);
            foreach($questions as $question) {
                $repository = $this->getDoctrine()->getRepository(Answer::class);
                $answers = $repository->findBy(["question" => $question->getId()]);
                $question->setAnswers($answers);
            };
            $submit = $request->request->get("formSubmit");
            if(!is_null($submit) && !empty($submit)) {
                foreach($questions as $question) {
                    if(!is_null($request->request->get("question" . $question->getId()))) {
                        foreach($question->getAnswers() as $answer) {
                            if($request->request->get("question" . $question->getId()) === $answer->getAnswer() && $answer->getExpectedAnswer()) {
                                $this->score++;
                            };
                            if($answer->getExpectedAnswer()) {
                                $expected = $answer->getAnswer();
                            };
                        };
                    };
                    $this->feedback["question" . $question->getId()] = ["given" => $request->request->get("question" . $question->getId()), "expected" => $expected];
                };
                $expected = null;
                $this->score = $this->score . "/" . sizeof($questions);
                if($this->getUser() !== NULL){
                    $this->saveResult($quizz, $this->score);
                } else {
                    $this->session = new Session();
                    if($this->session->get('history')){
                        $history = $this->session->get('history');
                    } else {
                        $history = [];
                    };
                    array_push($history, array('quizz' => $quizz, 'score' => $this->score, 'date' => new \DateTime()));
                    $this->session->set("history", $history);
                };
                return $this->render("quizz/quizz.html.twig", ["page" => $quizz->getName(), "categories" => $this->categories, "quizz" => $quizz, "score" => $this->score, "feedback" => $this->feedback]);
            };
            return $this->render("quizz/quizz.html.twig", ["page" => $quizz->getName(), "categories" => $this->categories, "quizz" => $quizz, "questions" => $questions]);
        }
        public function create($id, Request $request): Response {
            $this->setCategories();
            if(!is_null($request->query->get("name"))){
                $quizzId = $this->addQuizz($request, $id);
                return (is_null($quizzId)) ? $this->redirectToRoute("category", ["id" => $id]) : $this->render("quizz/create.html.twig", ["page" => "Création", "categories" => $this->categories, "nb_questions" => $request->query->get("nb_questions"), "quizzId" => $quizzId]);
            } else {
                return $this->render("quizz/create.html.twig", ["page" => "Création", "categories" => $this->categories]);
            };
        }
        public function saveQuizz(Request $request): Response {
            $quizzId = $request->request->get("quizz_id");
            for($i = 1; $i <= (count($request->request) - 1) / 6; $i++) {
                $questionString = $request->request->get("question" . $i);
                $questionId = $this->addQuestion($questionString, $quizzId);
                for($j = 1; $j <= 4; $j++) {
                    $answerString = $request->request->get("answer" . $j . "of" . $i);
                    $bool = false;
                    if(intval($request->request->get("good_answer" . $i)) === $j){
                        $bool = true;
                    };
                    $this->addAnswer($answerString, $questionId, $bool);
                };
            };
            return $this->redirectToRoute("quizz", ["id" => $quizzId]);
        }
        /* ------------------------------------------------------------------------------------------------- */
        /* Helpers */
        /* ------------------------------------------------------------------------------------------------- */
        public function setCategories() {
            $repository = $this->getDoctrine()->getRepository(Category::class);
            $categories = $repository->findBy([], ["name" => "ASC"]);
            return $this->categories = $categories;
        }
        public function addQuizz($request, $id) {
            $quizz = new Quizz();
            $repository = $this->getDoctrine()->getRepository(Quizz::class);
            $check = $repository->findBy(["name" => $request->query->get("name")]);
            if(sizeof($check) == 0) {
                $quizz->setName($request->query->get("name"));
                $repository = $this->getDoctrine()->getRepository(Category::class);
                $category = $repository->findOneById($id);
                $quizz->setCategory($category);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($quizz);
                $entityManager->flush();
                $lastId = $quizz->getId();
                return $lastId;
            } else {
                return null;
            };
        }
        public function addQuestion($questionString, $quizzId){
            $question = new Question();
            $repository = $this->getDoctrine()->getRepository(Question::class);
            $question->setQuestion($questionString);
            $repository = $this->getDoctrine()->getRepository(Quizz::class);
            $quizz = $repository->findOneById($quizzId);
            $question->setIdQuizz($quizz);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->flush();
            $lastId = $question->getId();
            return $lastId;
        }
        public function addAnswer($answerString, $questionId, $bool){
            $answer = new Answer();
            $repository = $this->getDoctrine()->getRepository(Answer::class);
            $answer->setAnswer($answerString);
            $answer->setExpectedAnswer($bool);
            $repository = $this->getDoctrine()->getRepository(Question::class);
            $question = $repository->findOneById($questionId);
            $answer->setIdQuestion($question);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($answer);
            return $entityManager->flush();
        }
        public function saveResult($quizz, string $score){
            $repository = $this->getDoctrine()->getRepository(History::class);
            $history = new History();
            $alreadyPlayed = $repository->findOneBy(["user" => $this->getUser()->getId(), "quizz" => $quizz->getId()]);
            if($alreadyPlayed === NULL ){
                $history->setIdUser($this->getUser());
                $history->setIdQuizz($quizz);
                $history->setScore($score);
                $history->setPlayedAt(new \DateTime());
            } else {
                $alreadyPlayed->setScore($score);
                $alreadyPlayed->setPlayedAt(new \DateTime());
                $history = $alreadyPlayed;
            };
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($history);
            return $entityManager->flush();
        }
    }
?>
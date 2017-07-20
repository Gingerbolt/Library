<?php
    date_default_timezone_set('America/Los_Angeles');
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Book.php";
    require_once __DIR__."/../src/Author.php";

    $app = new Silex\Application();

    $server = 'mysql:host=localhost:8889;dbname=library';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig');
    });

    $app->get("/checkout", function() use ($app) {
        return $app['twig']->render('checkout.html.twig', array('books' => Book::getAll()));
    });

    $app->post("/login_new", function() use ($app) {
        $new_name = $_POST['name'];
        $new_patron = new Patron($new_name);
        $new_patron->save();
        return $app['twig']->render('login.html.twig');
    });

    $app->get("/login", function() use ($app) {
        return $app['twig']->render('login.html.twig');
    });

    $app->get("/administration", function() use ($app) {
    return $app['twig']->render('books.html.twig', array('books' => Book::getAll(), 'authors_list' => Author::getAll()));
    });

    $app->post("/administration", function() use ($app) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $book = new Book($title, $content);
        $book->save();
        $author_array = explode(',', $_POST['author']);
        for($x=count($author_array)-1; $x>=0; --$x)
        {
            $new_author = new Author($author_array[$x]);
            $new_author->save();
            $book->setAuthor($new_author->getId());
        }
        return $app['twig']->render('books.html.twig', array('books' => Book::getAll()));
    });

    $app->get("/bookinfo/{id}", function($id) use ($app) {
        $current_book = Book::find($id);
        $authors = $current_book->getAuthors();
        $authors_list = Author::getAll();
        return $app['twig']->render('bookinfo.html.twig', array('book' => $current_book, 'authors' => $authors, 'authors_list' => $authors_list));
    });

    $app->delete("/delete_book/{id}", function($id) use ($app) {
        $current_book = Book::find($id);
        $current_book->deleteBook();
        return $app['twig']->render('books.html.twig', array('books' => Book::getAll()));
    });

    $app->patch("/edit_book_title/{id}", function($id) use ($app) {
        $new_title = $_POST['title_edit'];
        $current_book = Book::find($id);
        $current_book->updateTitle($new_title);
        $authors = $current_book->getAuthors();
        $authors_list = Author::getAll();
        return $app['twig']->render('bookinfo.html.twig', array('book' => $current_book, 'authors' => $authors, 'authors_list' => $authors_list));
    });

    $app->patch("/edit_book_content/{id}", function($id) use ($app) {
        $new_content = $_POST['content_edit'];
        $current_book = Book::find($id);
        $current_book->updateContent($new_content);
        $authors = $current_book->getAuthors();
        $authors_list = Author::getAll();
        return $app['twig']->render('bookinfo.html.twig', array('book' => $current_book, 'authors' => $authors, 'authors_list' => $authors_list));
    });

    $app->delete("/delete_book_author/{id}", function($id) use ($app) {
        $author_id = $_POST['author_id'];
        $current_book = Book::find($id);
        $current_book->removeAuthor($author_id);
        $authors = $current_book->getAuthors();
        $authors_list = Author::getAll();
        return $app['twig']->render('bookinfo.html.twig', array('book' => $current_book, 'authors' => $authors, 'authors_list' => $authors_list));
    });

    $app->post("/add_book_author/{id}", function($id) use ($app) {
        $author_id = $_POST['new_author'];
        $current_book = Book::find($id);
        $current_book->setAuthor($author_id);
        $authors = $current_book->getAuthors();
        $authors_list = Author::getAll();
        return $app['twig']->render('bookinfo.html.twig', array('book' => $current_book, 'authors' => $authors, 'authors_list' => $authors_list));
    });

    $app->post("/find_book_by_author", function() use ($app) {
        $author_id = $_POST['search_author'];
        $author = Author::find($author_id);
        $books = $author->getBooks();
        return $app['twig']->render('search_by_author.html.twig', array('books' => $books, 'author' => $author));
    });

    $app->get("/create_account", function() use ($app) {
        return $app['twig']->render('create_account.html.twig');
    });

    return $app;
?>

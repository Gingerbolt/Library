<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Book.php";
    require_once "src/Author.php";
    require_once "src/Patron.php";

    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BookTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Book::deleteAll();
            Patron::deleteAll();
            $GLOBALS['DB']->exec("DELETE FROM copies;");
        }

        function testGetTitle()
        {
            $title = "Hatchet";
            $content = "Boy has a dope ass axe and does hella shit with it";
            $new_book = new Book($title, $content);

            $result = $new_book->getTitle();

            $this->assertEquals($title, $result);
        }

        function testGetContent()
        {
            $title = "Bandito";
            $content = "Teryiaki chicken";
            $new_book = new Book($title, $content);

            $result = $new_book->getContent();

            $this->assertEquals($result, $content);
        }

        function testGetCopies()
        {
            $title = "Hatchet";
            $content = "Boy has a dope ass axe and does hella shit with it";
            $new_book = new Book($title, $content);

            $result = $new_book->getCopies();

            $this->assertEquals(3, $result);
        }

        function testSetCopies()
        {
            $title = "Shmatchet";
            $content = "Boy has a sweet ass axe and does hecka shit with it";
            $new_book = new Book($title, $content);
            $new_copy_count = 3;

            $new_book->setCopies($new_copy_count);
            $result = $new_book->getCopies();
            $this->assertEquals($new_copy_count, $result);
        }

        function testSave()
        {
            $title = "Ricky Rogers Runs";
            $content = "He runs and he sometimes jumps";
            $new_book = new Book($title, $content);

            $executed = $new_book->save();

            $this->assertTrue($executed, "The book was not succesfully saved to the database");
        }

        function testGetAll()
        {
            $title = "Big Gambino";
            $content = "He hits hella homeruns";
            $new_book = new Book($title, $content);
            $new_book->save();

            $title2 = "The Diver";
            $content2 = "He goes hella deep";
            $new_book2 = new Book($title2, $content);
            $new_book2->save();
            $result = Book::getAll();

            $this->assertEquals([$new_book, $new_book2], $result);
        }

        function testDeleteAll()
        {
            $title = "Big Gambino";
            $content = "He hits hella homeruns";
            $new_book = new Book($title, $content);

            $title2 = "The Diver";
            $content2 = "He goes hella deep";
            $new_book2 = new Book($title2, $content);

            Book::deleteAll();

            $result = Book::getAll();

            $this->assertEquals([], $result);
        }

        function testGetId()
        {
            $title = "The walk off";
            $content = "He walked off";
            $new_book = new Book($title, $content);
            $new_book->save();

            $result = $new_book->getId();

            $this->assertTrue(is_numeric($result));
        }

        function testFind()
        {
            $title = "Big Gambino";
            $content = "He hits hella homeruns";
            $new_book = new Book($title, $content);
            $new_book->save();

            $title2 = "The Diver";
            $content2 = "He goes hella deep";
            $new_book2 = new Book($title2, $content);
            $new_book2->save();
            $result = Book::find($new_book->getId());

            $this->assertEquals($new_book, $result);
        }

        function testUpdateTitle()
        {
            $title = "Zebra Stripes";
            $content = "black and white";
            $new_book = new Book($title, $content);
            $new_book->save();

            $new_name = "Tiger Stripe";
            $result = $new_book->updateTitle($new_name);

            $this->assertEquals($new_name, $new_book->getTitle());
        }

        function testUpdateContent()
        {
            $title = "The content content";
            $content = "It was the best of times, it was the blurst of times";
            $new_book = new Book($title, $content);
            $new_book->save();

            $new_content = "Na na na na na na na na Batman!";
            $result = $new_book->updateContent($new_content);

            $this->assertEquals($new_content, $new_book->getContent());
        }

        function testGetAuthors()
        {
            $title = "The content content";
            $content = "It was the best of times, it was the blurst of times";
            $new_book = new Book($title, $content);
            $new_book->save();

            $name = "fransisco";
            $new_author = new Author($name);
            $new_author->save();
            $new_author_id = $new_author->getId();

            $new_book->setAuthor($new_author_id);
            $result = $new_book->getAuthors();

            $this->assertEquals([$new_author], $result);
        }

        function testGetOverdueBooks()
        {
            $title = "Two brothers";
            $content = "Fighting the mexican armada. And there are angry grandmas with guns... and they cross... attack.";
            $newer_book = new Book($title, $content);
            $newer_book->save();
            $book_id = $newer_book->getId();

            $test_title = "Monro De frumont";
            $test_content = "lalalal gigigiggi haha har har ahr";
            $test_book = new Book($test_title, $test_content);
            $test_book->save();
            $test_id = $test_book->getId();

            $other_test_name = "Jiggigy Ji Jo";
            $other_test_content = "Hamaburgers";
            $other_test_book = new Book($other_test_name, $other_test_content);
            $other_test_book->save();

            $name = "Osiris";
            $new_patron = new Patron($name);
            $new_patron->save();

            $new_patron->checkoutBook($book_id);
            $new_patron->checkoutBook($test_id);

            $new_due_date = date("Y-m-d", strtotime('-1 week'));
            $GLOBALS['DB']->exec("UPDATE copies SET due_date = '{$new_due_date}' WHERE patron_id = {$new_patron->getId()};");

            $result = Book::getOverdueBooks();

            $this->assertEquals([$newer_book, $test_book], $result);
        }

        function testDeleteBook()
        {
            $title = "Two raptors";
            $content = "Fighting the mammelian armada. And there are angry grandmas with guns... and they cross... attack.";
            $newt_book = new Book($title, $content);
            $newt_book->save();
            $newt_id = $newt_book->getId();

            $name = "Mr. Authorson";
            $new_author = new Author($name);
            $new_author->save();

            $author_id = $new_author->getId();
            $newt_book->setAuthor($author_id);

            $newt_book->deleteBook();
            $result = Book::find($newt_id);
            $this->assertEquals(null, $result);
        }
    }

 ?>

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

    class PatronTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Patron::deleteAll();
            $GLOBALS['DB']->exec("DELETE FROM copies;");
            Book::deleteAll();
        }

        function testSave()
        {
            $name = "Trogdor";
            $new_patron = new Patron($name);

            $executed = $new_patron->save();

            $this->assertTrue($executed, "The patron was not succesfully saved to the database");
        }
        function testCheckoutBook()
        {
            $name = "Fredjesus";
            $new_patron = new Patron($name);
            $new_patron->save();

            $title = "Spalding";
            $content = "Relado";
            $new_book = new Book($title, $content);
            $new_book->save();
            $title_2 = "Monkeyball";
            $content_2 = "For the monkeys";
            $new_book_2 = new Book($title_2, $content_2);
            $new_book_2->save();

            $new_book_idz = $new_book->getId();
            $new_patron->checkoutBook($new_book_idz);

            $result = $new_patron->getBooks();
            $this->assertEquals([$new_book], $result);
        }

        function testGetBooks()
        {
            $name = "Fredjesus";
            $new_patron = new Patron($name);
            $new_patron->save();

            $titleed = "SPAZMO";
            $contented = "The spazziest superhero";
            $new_booklet = new Book($titleed, $contented);
            $new_booklet->save();

            $title_2 = "Monkeyball";
            $content_2 = "For the monkeys";
            $new_book_2 = new Book($title_2, $content_2);
            $new_book_2->save();

            $new_book_id = $new_booklet->getId();
            $new_book_id_2 = $new_book_2->getId();
            $new_patron->checkoutBook($new_book_id);
            $new_patron->checkoutBook($new_book_id_2);

            $result = $new_patron->getBooks();

            $this->assertEquals([$new_booklet, $new_book_2], $result);
        }
    }
?>

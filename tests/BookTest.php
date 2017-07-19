<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Book.php";
    require_once "src/Author.php";

    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BookTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Book::deleteAll();
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

            $this->assertEquals(1, $result);
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
    }

 ?>

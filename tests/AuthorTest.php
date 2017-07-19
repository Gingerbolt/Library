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

    class AuthorTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Author::deleteAll();
        }

        function testGetName()
        {
            $name = "Hatchet";
            $new_author = new Author($name);

            $result = $new_author->getName();

            $this->assertEquals($name, $result);
        }

        function testSave()
        {
            $name = "Ricky Rogers Runs";
            $new_author = new Author($name);

            $executed = $new_author->save();

            $this->assertTrue($executed, "The author was not succesfully saved to the database");
        }

        function testGetAll()
        {
            $name = "Big Gambino";
            $new_author = new Author($name);
            $new_author->save();

            $name_2 = "The Diver";
            $new_author_2 = new Author($name_2);
            $new_author_2->save();
            $result = Author::getAll();

            $this->assertEquals([$new_author, $new_author_2], $result);
        }

        function testDeleteAll()
        {
            $name = "Big Gambino";
            $new_author = new Author($name);
            $new_author->save();

            $name_2 = "The Diver";
            $new_author_2 = new Author($name_2);
            $new_author_2->save();

            Author::deleteAll();

            $result = Author::getAll();

            $this->assertEquals([], $result);
        }

        function testGetId()
        {
            $name = "James Whittlefinch";
            $new_author = new Author($name);
            $new_author->save();

            $result = $new_author->getId();

            $this->assertTrue(is_numeric($result));
        }

        function testFind()
        {
            $name = "Big Gambino";
            $new_author = new Author($name);
            $new_author->save();

            $name_2 = "The Diver";
            $new_author_2 = new Author($name_2);
            $new_author_2->save();

            $result = Author::find($new_author->getId());

            $this->assertEquals($new_author, $result);
        }

        function testUpdateName()
        {
            $name = "Zebra Stripes";
            $new_author = new Author($name);
            $new_author->save();

            $new_name = "Loquacious Larry";
            $result = $new_author->updateName($new_name);

            $this->assertEquals($new_name, $new_author->getName());
        }

        function testGetBooks()
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
            $result = $new_author->getBooks();

            $this->assertEquals([$new_book], $result);
        }
    }

 ?>

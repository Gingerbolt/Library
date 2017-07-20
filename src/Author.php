<?php
    class Author
    {
        private $name;
        private $id;

        function __construct($name, $id = null)
        {
            $this->name = $name;
            $this->id = $id;
        }

        function getName()
        {
            return $this->name;
        }

        function setName($new_name)
        {
            $this->name = $new_name;
        }

        function getId()
        {
            return $this->id;
        }

        function getBooks()
        {
            $returned_books = $GLOBALS['DB']->query("SELECT books.* FROM authors JOIN authorship ON (authorship.author_id = authors.id) JOIN books ON (books.id = authorship.book_id) WHERE authors.id = {$this->getId()};");
            $books = array();
            foreach($returned_books as $book) {
                $title = $book['title'];
                $content = $book['content'];
                $id = $book['id'];
                $new_book = new Book($title, $content, $id);
                array_push($books, $new_book);
            }
            return $books;
        }

        function save()
        {
            $executed = $GLOBALS['DB']->exec("INSERT INTO authors (name) VALUES ('{$this->getName()}');");
            if ($executed) {
                $this->id = $GLOBALS['DB']->lastInsertId();
                return true;
            } else {
                return false;
            }
        }

        static function getAll()
        {
            $returned_authors = $GLOBALS['DB']->query("SELECT * FROM authors;");
            $authors = array();
            foreach ($returned_authors as $author) {
                $name = $author['name'];
                $id = $author['id'];
                $new_author = new Author($name, $id);
                array_push($authors, $new_author);
            }
            return $authors;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM authors;");
        }

        static function find($search_id)
        {
            $returned_authors = $GLOBALS['DB']->prepare("SELECT * FROM authors WHERE id = :id;");
            $returned_authors->bindParam(':id', $search_id, PDO::PARAM_STR);
            $returned_authors->execute();
            foreach ($returned_authors as $author) {
                $name = $author['name'];
                $id = $author['id'];
                if ($id == $search_id) {
                    $new_author = new Author($name, $id);
                }
            }
            return $new_author;
        }

        function updateName($new_name)
        {
            $executed = $GLOBALS['DB']->exec("UPDATE authors SET name = '{$new_name}' WHERE id = {$this->getId()};");
            if ($executed) {
                $this->setName($new_name);
                return true;
            } else {
                return false;
            }
        }


    }


 ?>

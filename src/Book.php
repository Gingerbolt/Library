<?php
    class Book
    {
        private $title;
        private $content;
        private $id;

        function __construct($title, $content, $id = null)
        {
            $this->title = $title;
            $this->content = $content;
            $this->id = $id;
        }

        function getTitle()
        {
            return $this->title;
        }

        function setTitle($new_title)
        {
            $this->title = $new_title;
        }

        function getContent()
        {
            return $this->content;
        }

        function setContent($new_content)
        {
            $this->content = $new_content;
        }

        function getId()
        {
            return $this->id;
        }

        function setAuthor($new_author_id)
        {
            $executed = $GLOBALS['DB']->exec("INSERT INTO authorship (book_id, author_id) VALUES ('{$this->getId()}', '{$new_author_id}');");
            if ($executed) {
                return true;
            } else {
                return false;
            }
        }

        function getAuthors()
        {
            $returned_authors = $GLOBALS['DB']->query("SELECT authors.* FROM books JOIN authorship ON (authorship.book_id = books.id) JOIN authors ON (authors.id = authorship.author_id) WHERE books.id = {$this->getId()};");
            $authors = array();
            foreach($returned_authors as $author) {
                $name = $author['name'];
                $id = $author['id'];
                $new_author = new Author($name, $id);
                array_push($authors, $new_author);
            }
            return $authors;
        }

        function save()
        {
            $executed = $GLOBALS['DB']->exec("INSERT INTO books (title, content) VALUES ('{$this->getTitle()}', '{$this->getContent()}');");
            if ($executed) {
                $this->id = $GLOBALS['DB']->lastInsertId();
                return true;
            } else {
                return false;
            }
        }

        static function getAll()
        {
            $returned_books = $GLOBALS['DB']->query("SELECT * FROM books;");
            $books = array();
            foreach ($returned_books as $book) {
                $title = $book['title'];
                $content = $book['content'];
                $id = $book['id'];
                $new_book = new Book($title, $content, $id);
                array_push($books, $new_book);
            }
            return $books;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM books;");
        }

        static function find($search_id)
        {
            $returned_books = $GLOBALS['DB']->prepare("SELECT * FROM books WHERE id = :id;");
            $returned_books->bindParam(':id', $search_id, PDO::PARAM_STR);
            $returned_books->execute();
            foreach ($returned_books as $book) {
                $title = $book['title'];
                $content = $book['content'];
                $id = $book['id'];
                if ($id == $search_id) {
                    $new_book = new Book($title, $content, $id);
                }
            }
            return $new_book;
        }

        function updateTitle($new_title)
        {
            $executed = $GLOBALS['DB']->exec("UPDATE books SET title = '{$new_title}' WHERE id = {$this->getId()};");
            if ($executed) {
                $this->setTitle($new_title);
                return true;
            } else {
                return false;
            }
        }

        function updateContent($new_content)
        {
            $executed = $GLOBALS['DB']->exec("UPDATE books SET content = '{$new_content}' WHERE id = {$this->getId()};");
            if ($executed) {
                $this->setContent($new_content);
                return true;
            } else {
                return false;
            }
        }
    }


 ?>

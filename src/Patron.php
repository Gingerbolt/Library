<?php

    // require_once "src/Book.php";

    class Patron
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

        function save()
        {
            $executed = $GLOBALS['DB']->exec("INSERT INTO patrons (name) VALUES ('{$this->getName()}');");
            if ($executed){
                $this->id = $GLOBALS['DB']->lastInsertId();
                return true;
            } else {
                return false;
            }
        }

        function checkoutBook($book_id)
        {
            $date_time = date("Y-m-d", strtotime('+1 week'));
            $checked_out_book = Book::find($book_id);
            $current_copies = $checked_out_book->getCopies();
            $new_copies = $current_copies - 1;
            if ($new_copies >= 0){
                $GLOBALS['DB']->exec("INSERT INTO copies (book_id, patron_id, due_date) VALUES ({$book_id}, {$this->getId()}, '{$date_time}');");
                $checked_out_book->setCopies($new_copies);
                return true;
            } else {
                return false;
            }
        }


        function getBooks()
        {
            $returned_books = $GLOBALS['DB']->query("SELECT books.* FROM patrons JOIN copies ON (copies.patron_id = patrons.id) JOIN books ON (books.id = copies.book_id) WHERE patrons.id = {$this->getId()};");

            $books = array();
            foreach($returned_books as $book) {
                $title = $book['title'];
                $content = $book['content'];
                $id = $book['id'];
                $copies = $book['copies'];
                $new_book = new Book($title, $content, $id, $copies);
                array_push($books, $new_book);
            }
            return $books;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons;");
        }
    }
?>

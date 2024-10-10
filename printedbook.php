<?php
require_once 'book.php';

class PrintedBook extends Book {
    private $numberOfPages;

    public function __construct($title, $author, $year, $numberOfPages) {
        parent::__construct($title, $author, $year);
        $this->numberOfPages = $numberOfPages;
    }

    public function getNumberOfPages() {
        return $this->numberOfPages;
    }
}
?>

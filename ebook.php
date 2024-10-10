<?php
require_once 'book.php';

class EBook extends Book {
    private $fileSize;

    public function __construct($title, $author, $year, $fileSize) {
        parent::__construct($title, $author, $year);
        $this->fileSize = $fileSize;
    }

    public function getFileSize() {
        return $this->fileSize;
    }
}
?>

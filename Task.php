<?php
class Task {
    private $id;
    private $description;
    private $completed;

    public function __construct($description, $completed = false) {
        $this->description = $description;
        $this->completed = $completed;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function isCompleted() {
        return $this->completed;
    }

    public function setCompleted($completed) {
        $this->completed = $completed;
    }
}
?>

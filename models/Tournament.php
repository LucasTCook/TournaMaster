<?php
require_once 'Model.php';

class Tournament extends Model{
    public $id;
    public $name;
    public $date;
    public $completed_at;
    public $creator_user_id;
    public $active;
}

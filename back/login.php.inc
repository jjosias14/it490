<?php
class logintoDB{
    private $logintoDB;
    public function __construct()
    {
        $this->logintoDB = new mysqli("localhost","joshua@localhost","12345","IT490DB");
        if ($this->logintoDB->connect_errno !=0)
        {
            echo "Error connecting to DB " .$this->logintoDB->connect_error.PHP_EOL;
            exit(1);
        }
        echo "Connected!".PHP_EOL;
    }
    public function validation($email,$password)
    {
        $email = $this->logintoDB->real_escape_string($email);
        $password = $this->logintoDB->real_escape_string($password);
        $selectStatement = "select * from Users where email = '$email'";
        $response = $this->logintoDB->query($selectStatement);

        while ($row = $response->fetch_assoc())
        {
            echo "Checking the password for $email".PHP_EOL;
            if ($row["password"]== $password)
            {
                echo "passwords are a go for $email".PHP_EOL;
                return 1;
            }
            echo "passwords are not a go for $email".PHP_EOL;
        }
        return 0;
    }
}
?>
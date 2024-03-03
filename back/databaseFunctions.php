#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('signIn.php.inc');
require_once('dataBaseconnect.php');
require_once('databaseClient.php');

//function to create session
function createSession($email) {
    $mydb = dataBaseConnect();
    $sessionID = SHA1($email.time());
    $sessionQuery = "INSERT INTO Sessions VALUES ('$email', '$sessionID', NOW())";
    $result = $mydb->query($sessionQuery);
    return $sessionID;
}

//function to valid session
function validateSession($sessionID) {
    $mydb = dataBaseConnect();
    $query = "SELECT UNIX_TIMESTAMP(creationTime) as epoch FROM Sessions WHERE sessionID = '$sessionID'";
    $result = $mydb->query($query);
    $row = $result->fetch_assoc();
    $epoch = intval($row['epoch']);
    $timeElapsed = time()-$epoch;
    if ($timeElapsed > 1200) {
        $deleteSession = "DELETE FROM Sessions WHERE sessionID = '$sessionID'";
        $result = $mydb->query($deleteSession);
        return json_encode(['valid' => 0]);
    }
    else {
        $updateSession = "UPDATE Sessions SET creationTime = NOW() WHERE sessionID = '$sessionID'";
        $result = $mydb->query($updateSession);
        return json_encode(['valid' => 1]);

    }
}

//function for user login
function doLoginto($email, $password) {
    $mydb = dataBaseConnect();
    $hash = SHA1($password);
    $query = "SELECT * FROM Users WHERE email = '$email' AND password = '$hash'";
    $result = $mydb->query($query);
    $user = $result->fetch_assoc();
    $first = $user['first'];
    $last = $user['last'];
    if ($result->num_rows == 1) {
        return json_encode(['f_name' => $first, 'l_name' => $last, 'email' => $email, 'sessionID' => createSession($email)]);
    }
    else {
        return json_encode(['message' => 'wrong email/password']);
    }
}

//function for user registration
function registerUser($first, $last, $email, $password) {
    $mydb = dataBaseConnect();
    $hash = SHA1($password);
    $query = "SELECT * FROM Users WHERE email = '$email'";
    $result = $mydb->query($query);
    if ($result->num_rows == 1 ) {
        return json_encode(['message' => 'That email address is in use']);
    }
    else {
        $registerQuery = "INSERT INTO Users VALUES ('$first', '$last','$email', '$hash')";
        $result =$mydb->query($registerQuery);
        return json_encode(['fname' => $first, 'lname' => $last, 'email' => $email, 'sessionID' => createSession($email)]);
    }
}

//function to retrieve email from sessionID
function selectEmailFromSession($sessionID) {
    $mydb = dataBaseConnect();
    $query = "SELECT email FROM Sessions WHERE sessionID = '$sessionID'";
    $result = $mydb->query($query);
    $session = $result->fetch_assoc();
    if ($result->num_rows == 1) {
        return $session['email'];
    }
}

//function to save/rate recipe
function saveRateRecipe($sessionID, $recipe) {
    $mydb = dbConnection();
    $email = selectEmailFromSession($sessionID);
    $recipeID = $recipe["id"];
    $title = $recipe["title"];
    $imgURL = $recipe["image"];
    $sourceURL = $recipe["sourceUrl"];
    $rating = $recipe["rating"];
    $query = "INSERT INTO Saved_Rated_Recipes (email, recipeID, title, image, sourceUrl, rating) VALUES ('$email','$recipeID', '$title', '$imgURL', '$sourceURL', '$rating') ON DUPLICATE KEY UPDATE rating = $rating";
    $result = $mydb->query($query);
    return json_encode(["message" => "succesfully saved/rated"]);
}



//function to view rated recipes for each user
function viewRatedRecipes($sessionID) {
    $mydb = dataBaseConnect();
    $email = selectEmailFromSession($sessionID);
    $query = "SELECT * FROM Saved_Rated_Recipes WHERE email = '$email'";
    $result = $mydb->query($query);
    if ($result->num_rows == 0) {
        echo "you have not rated any recipes";
        return json_encode(["message" => "you have not rated any recipes"]);
    }
    else {
        $userRatedRecipes = $result->fetch_all(MYSQLI_ASSOC);
        return json_encode(["userRatedRecipes" => $userRatedRecipes]);
    }
}
//function to search for a recipe using a keyword
function searchKeywordRecipe($keyword) {
    $mydb = dbConnection();
    $query = "SELECT * FROM Recipes WHERE title LIKE '%$keyword%'";
    $result = $mydb->query($query);
    if ($result->num_rows == 0) {
        $response = dbClient(["type" => "keywordrecipe", "keywordrecipe" => $keyword]);
        echo $response;
        return $response;
    }
}
?>
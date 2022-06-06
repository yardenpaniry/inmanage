<?php
include 'Database.php';
?>
<style>
    <?php include 'style.css'; ?>
</style>
<html>

<head>
    <title>inmanage test</title>
</head>

<body>
    <?php
    function getArrayFromDatabase($url)
    {
        if ($url == "posts")
            $url = "https://jsonplaceholder.typicode.com/posts";
        elseif ($url == "users")
            $url = "https://jsonplaceholder.typicode.com/users";

        // Initialize a CURL session.
        $ch = curl_init();

        //Disable CURLOPT_SSL_VERIFYHOST and CURLOPT_SSL_VERIFYPEER by
        //setting them to false.
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Return Page contents.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //grab URL and pass it to the variable.
        curl_setopt($ch, CURLOPT_URL, $url);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        $data = json_decode($result, true);

        return $data;
    }

    $con = new Database();
    $con->connect();

    $postsArray = getArrayFromDatabase("posts");
    $usersArray = getArrayFromDatabase("users");

    $count = 0;

    foreach ($usersArray as $row) //Extract the Array Values by using Foreach Loop
    {

        $queryArray =  array($row["id"], $row["name"], $row["email"]);
        $con->insert("users", $queryArray, "userId, name, email");

        $table_data_users .= '
            <tr>
            <td>' . $row["id"] . '</td>
            <td>' . $row["name"] . '</td>
            <td>' . $row["email"] . '</td>
            </tr>
                ';
    }
    foreach ($postsArray as $row) //Extract the Array Values by using Foreach Loop
    {
        $count++;
        if ($count == 51) //limit to 50 posts
            break;

        $queryArray =  array($row["userId"], $row["id"], $row["title"], $row["body"]);
        $con->insert("posts", $queryArray, "userId, id, title, body");

        $con->update("users", "`active`='1'", "userId = " . $row["userId"]);

        $table_data .= '
            <tr>
            <td>' . $row["userId"] . '</td>
            <td>' . $row["id"] . '</td>
            <td>' . $row["title"] . '</td>
            <td>' . $row["body"] . '</td>
            </tr>';
    }

    echo '<h1>Posts table</h1><br />';
    echo '
    <table class="table table-bordered">
      <tr>
      <th>user id </th>
      <th>id</th>
      <th>title</th>
      <th>body</th>
      </tr>';
    echo $table_data;
    echo '</table>';


    echo '<h1>Users table</h1><br />';
    echo '
    <table class="table table-bordered">
      <tr>
      <th >user id </th>
      <th >name</th>
      <th >email</th>
      </tr>';
    echo $table_data_users;
    echo '</table>';

    ?>
</body>

</html>
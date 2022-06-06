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

        $con->update("users", "`active`='1'", "userId = " . $row["userId"]); //change active to true when there is post of users

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

    //get pic from url 
    $image = 'https://cdn2.vectorstock.com/i/1000x1000/23/81/default-avatar-profile-icon-vector-18942381.jpgs';
    $imageData = base64_encode(file_get_contents($image));


    $result = $con->select('users', "*", "`active`='1'");  //get just the users that active

    if (mysqli_num_rows($result) > 0) {

        $strPrint = "<div class='social'>";
        // output data of each row
        while ($row = mysqli_fetch_assoc($result)) {
            $strPrint .=  "<div class='user'>";
            $strPrint .= '<img src="data:image/jpeg;base64,' . $imageData . '">';
            $strPrint .= "<div class='userInfo'>
                            <div class='title'>id: </div>
                                <p> " . $row["userId"] . "</p>
                            <div class='title'>Name: </div> 
                                <p>" . $row["name"] . "</p>
                            <div class='title'>email: </div>
                                <p> " . $row["email"] . "</p>";

            $strPrint .= "<div class='posts'><h2>posts: </h2>";

            $posts = $con->select('posts', "*", "`userId`='" . $row["userId"] . "'");

            while ($post = mysqli_fetch_assoc($posts)) {

                $strPrint .= "<div class='post'><div class='title'> post number: </div>";
                $strPrint .= " <p> " . $post["id"] . "</p>";
                $strPrint .= "<div class='title'>title: </div>";
                $strPrint .= " <p> " . $post["title"] . "</p>";
                $strPrint .= "<div class='title'>body: </div>";
                $strPrint .= "<p> " . $post["body"] . "</p> </div>";
            }

            $strPrint .= "</div></div></div>";
        }

        $strPrint .=  "</div>";
        echo $strPrint;
    } else {
        echo "no results";
    }

    echo "<div class='part6'>";

    $result = $con->select('users', "*", "MONTH(birthday) = MONTH(NOW())");  //get just the users that have birthday this month

    if (mysqli_num_rows($result) > 0) {

        $strPrint = "<h1>celebrating a birthday this month:</h1>
                        <div class='birthdayPart'>";
        // output data of each row
        while ($row = mysqli_fetch_assoc($result)) {


            $post = $con->select('posts', "id", "userId=" . $row["userId"],  " id DESC LIMIT 1");

            if (mysqli_num_rows($post) > 0) {

                $strPrint .= "<div class='birthdayPeople'>
                            <div class=''>id: </div>
                                 " . $row["userId"] . "
                            <div class=''>Name: </div> 
                                " . $row["name"];
                $row = mysqli_fetch_assoc($post);
                $strPrint .= "<br>last post ID: " . $row["id"];
            }

            $strPrint .= "</div>";
        }
        $strPrint .= "</div>";
        echo $strPrint;
    } else {
        echo "no results";
    }

    echo "</div>";


    ?>
</body>

</html>
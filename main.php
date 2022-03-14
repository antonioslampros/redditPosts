<?php

$curl = curl_init();
require_once "config.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Create account on RapidAPI and subscribe to reddit API to test the following script
curl_setopt_array($curl, [
    CURLOPT_URL => "https://reddit3.p.rapidapi.com/subreddit?url=https%3A%2F%2Fwww.reddit.com%2Fr%2FSatoshiStreetBets&filter=new",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: reddit3.p.rapidapi.com",
        "x-rapidapi-key: 3a1107fda8msh9eddc2e29601754p195cdbjsnd63bbb28f823"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    //echo $response;
}
//var_dump(json_decode($response, true));
$posts = json_decode($response)->posts;
//var_dump($posts);

foreach($posts as $post){
    //echo gmdate("Y-m-d\TH:i:s\Z", $post->created);

    $select = mysqli_query($conn, "SELECT * FROM posts WHERE title = '".filter_var($post->title, FILTER_SANITIZE_STRING)."'");
    if(mysqli_num_rows($select)) { //Upload only unique posts
    } else {
        $query = "INSERT INTO posts (title, created, author_fullname, subreddit, upvote_ratio) VALUES ('". filter_var($post->title, FILTER_SANITIZE_STRING)."','". date("Y-m-d H:i:s", $post->created)."','". $post->author_fullname."','". $post->subreddit."','". $post->upvote_ratio."')";
        $q = mysqli_query($conn, $query) or die (mysqli_error($conn));
    }

}
<?php
ini_set('display_errors', 1);

$query = $_POST["search"];

$api_key = '2ac046e5a4aef7162272fd490bb0ee32';
$perPage = '25';

$url = 'https://api.flickr.com/services/rest/?method=flickr.photos.search';
$url.= '&api_key='.$api_key;
$url.= '&tags='.urlencode($query);
$url.= '&per_page='.$perPage;
$url.= '&extras=url_sq,url_t,url_s,url_q,url_m,url_n,url_z,url_c,url_l,url_o';
$url.= '&format=json';
$url.= '&nojsoncallback=1';
$requestMethod = 'POST';


$requestMethod = 'GET';
$response = file_get_contents($url);
//echo $response;

//connect to server, connect to database
    $connect = new mysqli('localhost', 'root', 'root','flickr');

        $json = json_decode($response);
        //Writes the beginning of the one replace statement
        //we use replace instead of insert because if we insert a post that already exists we will get
        //a duplicate key error. Replace will delete the old row and replace it with a new row if we attempt to
        //insert row with a key that was already used
        $insert = "REPLACE INTO flickr.results (search, id,owner,secret,server,title, ispublic,isfriend,isfamily,url_n) VALUES ";

        //loops through each post in the JSON file
        foreach($json->photos->photo as $postInformation){

            //concatenates all of the information needed for a post in between parenthesis and separated by comments
            //It writes the part of the insert statement that we need for each post
            //because title can apostrophes in it, we put it in a variable first so that we can put it in a function
            //that escapes the apostrophes
            $values  = "('".$query."' , '";
            $values .= $postInformation->id."' , '";
            $values .= $postInformation->owner."' , '";
            $values .= $postInformation->secret."' , '";
            $values .= $postInformation->server."' , '";
            $values .= mysqli_real_escape_string($connect,$postInformation->title)."' , '";
            $values .= $postInformation->ispublic."' , '";
            $values .= $postInformation->isfriend."' , '";
            $values .= $postInformation->isfamily."' , '";
            $values .= mysqli_real_escape_string($connect,$postInformation->url_n)."'),";
            //appends each post to the replace statement so that we can send all of the posts to the database
            //at once instead of one at a time
            $insert = $insert.$values;
        }//end foreach loop

        //We concatenated a comma at the end each of the posts $value statement to compose our one query.
        //However for the one, we do not want that comma, so we get rid of it and add a semicolon to the end
        //to complete our insert query.

        $insert2 = substr($insert,0, -1).";";
        //echo $insert2;

        //Run the query that was written or show an error if it can't run
        //$insertComplete = mysql_query($insert2,$connect)or die('Tried to run the insert, here was the error I received: '.mysql_error());
        if (mysqli_query($connect, $insert2)) {
            //echo "New record created successfully";
        } else {
            echo "Error: " . $insert2 . "<br>" . mysqli_error($connect);
        }
        
        echo "<h2>".$query ."</h2>";
        $searchresults = "select a.url_n
                    from flickr.results a
                    where a.search = '".$query."' and url_n is not null and trim(url_n) <> '' order by last_update desc;";
        if(!$result = $connect->query($searchresults)){
            die('There was an error running the query [' . $connect->error . ']');
        } else {
            while($row = $result->fetch_assoc()){
                //echo $row['id_str'] . '<br />' . $row['created_at'] . '<br />' . $row['user_id'] . '<br />' . $row['textf']. '<br />';
                echo '<img src="'.$row['url_n'].'" alt="'.$query.'" style="width:304px;height:228px">';
            }
            $result->free();
        }
        
        
//close database connection
   mysqli_close($connect);
  
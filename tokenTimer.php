<?php    
    include 'database.php';
    $con = $GLOBALS['db_conn'];    
    $token = $argv[1];

    $query = "SELECT auth_status FROM registration WHERE auth_token = '$token'";
    $result = mysqli_query($con, $query);
    $rowcount = mysqli_num_rows($result);

    if (mysqli_num_rows($result) == 1) 
    {

        $rowcount = mysqli_fetch_assoc($result);
        $count = 1;

        while($rowcount['auth_status'] !== 'closed')
        {
            $result = mysqli_query($con, $query);
            $rowcount = mysqli_num_rows($result);
            $rowcount = mysqli_fetch_assoc($result);

            if($count == 60 * 2)
            {
                $queryu = "UPDATE registration SET auth_status = 'closed' WHERE auth_token = '$token'";
                mysqli_query($con, $queryu);  
                break;
            } 

            $count = $count + 1;
            usleep(1000000);
        }

        exit();
    }
    else
    {
        exit();
    }   

?>
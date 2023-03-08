<?php
    include 'database.php';

    // error_reporting(E_ERROR | E_PARSE);
    $con = $GLOBALS['db_conn'];    
    $auth_req = $_REQUEST["auth"];
    $isInvalidAuth = false;
    $data = "";
    $newDataFormat = "";

    try 
    {
        $auth = AesCBC256_Decryption("lkfjoiajf", $auth_req);

        if($auth !== null)
        {
            $auth = explode(":", $auth);
            $college = $auth[1];
            if($auth[0] !== "true")
            {
                DisplayErrorMessage("Invalid auth token !");
            }
            else
            {
                $query = "SELECT auth_status FROM registration WHERE auth_token = '$auth_req'";

                $result = mysqli_query($con, $query);

                $countR = mysqli_num_rows($result);

                if (mysqli_num_rows($result) == 1) 
                {
                    $row = mysqli_fetch_assoc($result);
                
                    if($row["auth_status"] == "created" || $row["auth_status"] == "opened")
                    {
                        $query = "SELECT * FROM registration WHERE auth_token = '$auth_req'";
                        $result = mysqli_query($con, $query);
                        $rowCount = mysqli_num_rows($result);

                        if (isset($_POST['register'])) 
                        {
                            $data = $_POST['data'];

                            for($i =0; $i < count($data); $i++)
                            {
                                $newDataFormat .= $data[$i] . " | " . $data[$i + 1] . ", ";
                                $i += 1;
                            }

                            if ($rowCount == 1)
                            { 
                                $query = "UPDATE registration SET datas = '$newDataFormat', college_name = '$college',  auth_status = 'closed' WHERE auth_token = '$auth_req'";
                                if (!mysqli_query($con, $query)) 
                                {
                                    ErrorMessage($auth_req, "Error in closing session");
                                }
                                else
                                {
                                    header('Location: poster.php?id=' . hash('sha256', $auth_req));
                                    exit();
                                }
                                
                            }
                            else
                            {
                                ErrorMessage($auth_req, "Auth Token is already exists !");
                            }

                        
                        }
                        else
                        {
                            if ($rowCount == 1)
                            { 
                                $query = "SELECT auth_status FROM registration WHERE auth_token = '$auth_req'";
                                $resultTR = mysqli_query($con, $query);
                                $countTR = mysqli_num_rows($result);
                
                                if (mysqli_num_rows($resultTR) == 1) 
                                {
                                    $rowTR = mysqli_fetch_assoc($resultTR);
                                    if($rowTR["auth_status"] == "created")
                                    {
                                        $query = "UPDATE registration SET auth_status = 'opened' WHERE auth_token = '$auth_req'";
                                        if (!mysqli_query($con, $query)) 
                                        {
                                            ErrorMessage($auth_req, "Error in opening session");
                                        }
                                        // else
                                        // {   
                                        //     $file = dirname(dirname(getcwd())) . "\\php\\php.exe";
                                        //     $arg = getcwd()  . "\\tokenTimer.php" . " " . $auth_req;

                                        //     $WshShell = new COM("WScript.Shell");
                                        //     $oExec = $WshShell->Run($file . " " . $arg, 0);
                                        // }
                                    }
                                }
                                else
                                {
                                    DisplayErrorMessage("Auth token is expired or already exists !");
                                    exit();
                                }
                            }
                            else
                            {
                                ErrorMessage($auth_req, "Auth token is already exists !");
                            }
                        }
                    }
                    else
                    {
                        DisplayErrorMessage("Auth token is expired or already exists !");
                        exit();
                    }
                }
                else if(mysqli_num_rows($result) == 0)
                {
                    ErrorMessage($auth_req, "Error in creating session");
                }
                else
                {
                    ErrorMessage($auth_req, "Not sure what is going on !");
                    exit();
                }
            }
        }
        else
        {
            DisplayErrorMessage("Invalid auth token !");
        }

    }
    catch(Error $e)
    {
        ErrorMessage($auth_req, $e);
    }
   
    mysqli_close($con);

    function AesCBC256_Encryption($password, $content)
    {
        $method = 'aes-256-cbc';
        $key = substr(hash('sha256', $password, true), 0, 32);
        $iv = chr(0x7b) . chr(0x20) . chr(0x4b) . chr(0x72) . chr(0x79) . chr(0x70) . chr(0x74) . chr(0x65) . chr(0x78) . chr(0x5f) . chr(0x4f) . chr(0x5f) . chr(0x6f) . chr(0x20) . chr(0x7d) . chr(0x00);
        $encryptedContent = base64_encode(openssl_encrypt($content, $method, $key, OPENSSL_RAW_DATA, $iv));
        return $encryptedContent;
    }

    function AesCBC256_Decryption($password, $content)
    {
        $method = 'aes-256-cbc';
        $key = substr(hash('sha256', $password, true), 0, 32);
        $iv = chr(0x7b) . chr(0x20) . chr(0x4b) . chr(0x72) . chr(0x79) . chr(0x70) . chr(0x74) . chr(0x65) . chr(0x78) . chr(0x5f) . chr(0x4f) . chr(0x5f) . chr(0x6f) . chr(0x20) . chr(0x7d) . chr(0x00);
        $encryptedContent = openssl_decrypt(base64_decode($content), $method, $key, OPENSSL_RAW_DATA, $iv);
        return $encryptedContent;
    }

    function ErrorMessage($token, $error)
    {
        echo "<div class=\"Status\" style=\"color: red\"> Something Went Wrong Contact Admin's ! </div>";
        $value = "Token: " . $token . " | " . $error . "\n";
        file_put_contents('ErrorLogs.txt', $value,  FILE_APPEND | LOCK_EX);     
        exit();
    }

    function DisplayErrorMessage($message)
    {
        echo "<div class=\"Status\" style=\"color: red\">" . $message . "</div>";
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body>
    <div class="NavHeading">
       LOGO
    </div>
    <div class="SubHeading">
        <div class="s1">
            Registration form for
            <br>
            <span name="college"><?php echo $college; ?><span>
        </div>
    </div>
    <div class="mainContainer">
        <div class="container">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?auth=" . $auth_req; ?>" method="post">
                <div class="form-group">
                    <label style="font-weight: bold;">Student Name:</label>
                    <input type="text" name="data[]" required>
                </div>
                <div class="form-group">
                    <label style="font-weight: bold;">Event:</label>
                    <select name="data[]" required>
                        <option value="">Select an event</option>
                        <option value="HTML">HTML</option>
                        <option value="PHP">PHP</option>
                        <option value="CSS">CSS</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" name="register">Register</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

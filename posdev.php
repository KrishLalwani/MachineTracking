<?php
    session_start();
    require_once "pdo.php";
    if( !isset($_SESSION['id']) )
    {
        die('ACCESS DENIED');
    }
    if( $_SESSION['role'] != '0' )
    {
        die('ACCESS DENIED');
    }
    if(isset($_POST['cancel']))
    {
        header("Location: viewdev.php");
        return;
    }
    if(isset($_POST['submit']) )
    {
        
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM hardware WHERE hardware_id = :hid');
                $stmt->execute(array(':hid' => $_POST['dev_id']));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row['COUNT(*)'] !== '0')
                {
                     $stmt1 = $pdo->prepare('SELECT * FROM lab WHERE name = :lab');
                    $stmt1->execute(array(':lab' => $_POST['lab']));
                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $lid = $row['lab_id'];

                     $stmt = $pdo->prepare('INSERT INTO hardware_position (hardware_id, lab_id, initial_date, final_date) VALUES (:hid, :lid, :id, :fd)');
                        $stmt->execute(array(':hid' => $_POST['dev_id'], ':lid' => $lid, ':id' => date('y-m-d'), ':fd'=> "0000-00-00"));

                    $stmt = $pdo->prepare('UPDATE hardware SET state = 1 where hardware_id = :hid');
                    $stmt->execute(array(':hid' => $_POST['dev_id']));

                    $_SESSION['success'] ="Device placed Successfully<br>";
                }
                else
                {
                    $_SESSION['error'] = "Device does not Exists<br>";
                }
            
            header('Location: viewdev.php');
            return;
        
    }
?>
<html>
<head>
    <title>Machine Tracking</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width = device-width, initial-scale = 1">

    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
    <link rel="stylesheet" type="text/css" href="style5.css">

    <style>
        .input-group-addon {
        min-width:150px;
        text-align:left;
    }
    </style>
</head>
<body>
            <div class="wrapper">
            <!-- Sidebar Holder -->
       <?php if (isset($_SESSION['id'])&&$_SESSION['role']=='0') include "navbar.php"; 
                else if(isset($_SESSION['id'])&&$_SESSION['role']=='1')  include "navbar_faculty.php";
                else include "navbar_tech.php";?>
   <div class="container-fluid row" id="content">

    <div class="page-header">
    <h1>PLACE DEVICE</h1>
    </div>
    <?php
        if ( isset($_SESSION['error']) )
        {
            echo('<p style="color: red;">'.$_SESSION['error']."</p>\n");
            unset($_SESSION['error']);
        }
        if ( isset($_SESSION['success']))
        {
            echo('<p style="color: green;">'.$_SESSION['success']."</p>\n");
            unset($_SESSION['success']);
        }
    ?>

    <form method="POST" action="posdev.php" class="col-xs-5">
    <input type="hidden" name="dev_id" value="<?= $_GET['dev_id'] ?>" class="btn btn-info">
    <div class="input-group">
    <span class="input-group-addon">LAB NAME </span>
    <select class="form-control" name="lab" required>
        <?php
            $read=$pdo->query('select name,lab_id from lab order by name');
            while($row = $read->fetch(PDO::FETCH_ASSOC))
            {
                $labname=$row['name'];
                $labid=$row['lab_id'];
                echo '<option name = $labid>';
                echo    $labname;
                echo '</option>';
            }
        ?>
    </select>
    </div><br/>
    <input type="submit" name="submit" value="submit" class="btn btn-info">
    <a class ="link-no-format" href="home.php"><div class="btn btn-my">Cancel</div></a>
    </form>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>

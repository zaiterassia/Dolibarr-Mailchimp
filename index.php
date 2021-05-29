<?php
ini_set('display_errors', 'on');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Dolibarr-Mailchimp</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="src/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
</head>
<body>

<div class="container-fluid">
  <div class="row justify-content-md-center">
    <div class="col-sm-6">
      <div class="card-body">
        <h4>Cette application permet d'exporter des contacts depuis la base de donnée Dolibarr vers Mailchimp</h4>

        <?php
        if(isset($_SESSION["error"])) {?>
              <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $_SESSION['msg']; ?>
              </div> 
              <?php
            unset($_SESSION['error']);
          }?>
        <form method="post" id="exportForm">
          <div class="form-group" style="margin-top: 60px;">
            <label for="mailchimpApiKey">Mailchimp api key</label>
            <input type="text" class="form-control" id="mailchimpApiKey" name="mailchimpApiKey" placeholder="API key">
          </div>
          <div class="form-group">
            <label for="mailchimpListeId">Id de la liste</label>
            <input type="text" class="form-control" id="mailchimpListeId" name="mailchimpListeId" placeholder="List Id">
          </div>
          <div style="text-align: center">    
            <button id="export" type="submit" class="btn btn-primary">Exporter</button>
          </div>
          <div class="d-flex justify-content-md-center">
            <img src="img/ajax-loader-2.gif" alt="" width="20" id="loader" style="display: none; margin: 10px;">
            <span id="info" style="margin: 10px;"></span>
          </div>
        </form>
        <br><br>
        <div class="progress" style="display: none; height: 5px;">
          <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
          <span class="sr-only">70% Complete</span>
          </div>
        </div>
        <br> <br>
        <div class="result" style="text-align: center"></div>
      </div>
    </div>
  </div>
</div>

<script src="src/jquery.min.js"></script>
<script src="src/bootstrap.min.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $("#export").on('click', function(e){
        e.preventDefault();
        $.ajax({
          url: 'export.php',
          type: 'post',
          dataType: 'json',
          data: $("#exportForm").serialize(),
          success:function(data){
            console.log(data);
              init();
              get_details();
          }
        });
        
      });
  });

  function get_details(){
    $.ajax({
      url: 'details.php',
      type: 'post',
      dataType: 'json',
      data: $("#exportForm").serialize(),
      success:function(data){
          var total = data.total_operations;
          console.log(total);
          var finished = data.finished_operations;
          console.log(finished);
          var per = Math.trunc(finished/total *100);
          console.log(per);
          $(".progress-bar").css("width", per +"%");
          $(".progress-bar").attr("aria-valuenowa", per);
        if(data.status=="finished"){
          $("#export").prop( "disabled", false );
          $("#loader").hide();
          $("#info").html("");
          //$(".progress").hide();
          string = "<p> Total des opérations: "+ data.total_operations + "<br>";
          //string += "Opérations finies: "+ data.finished_operations + "<br>";
          string += 'Opérations en échec: <span style="color:red;"><b>' + data.errored_operations + '</b></span><br>';
          success = data.finished_operations - data.errored_operations ;
          if (success){
          string += '<i class="fa fa-check-circle" style="color:green; font-size:20px"></i> ' + success + " membre(s) ont été ajouté(s) avec succès <br><br>";
          }
          string+= 'Pour plus d\'information, télécharger le fichier de réponse: <a href= "'+ data.response_body_url  + '"> en cliquant  ici </a></p>';
          $(".result").html(string);
          console.log(data.total_operations);
          console.log(data. finished_operations);
          console.log(data.errored_operations);
          console.log(data.response_body_url);
        }
        else{
          console.log(data.status);
         $("#info").html(data.status + "...");
          setTimeout(get_details, 1000);
        }
      }
    });

  }

  function init(){
    $(".result").html("");
    $("#export").prop( "disabled", true );
    $("#loader").show();
    $("#info").html("pending...");
    $(".progress").show();
    $(".progress-bar").css("width", "0%");
    $(".progress-bar").attr("aria-valuenowa", 0);


  }
</script>

</body>
</html>

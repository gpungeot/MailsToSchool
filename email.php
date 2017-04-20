<?php

	const Camille = 0;
	const Arthur = 1;

	class EMail
	{
	    private $child;
	    private $goNogo;
	    private $beginDate = null;
	    private $endDate = null;
	    private static $mailBodyParts = array(
	    	"genericStart" => "Bonjour,\nPour information mon enfant ",
			"go" => "ira ",
			"noGo" => "n'ira pas ",
			"genericKinderGarden" => "au centre de loisirs ",
			"genericRegards" => ".\n\nBonne journée.\nCordialement\nGuillaume Pungeot",
			);
	    private $childNames = [Camille => "Camille", Arthur => "Arthur"];
	    private $kinderGarden = [Camille => "CLP ", Arthur => "CLM "];
	    private $kinderGardenMails = [Camille => "Periscolaire Louveciennes <perisco@louveciennes.fr>, centre de loisirs primaire <clp@louveciennes.fr>, Ecole Leclerc - Primaire <0780595Y@ac-versailles.fr>", Arthur => "Periscolaire Louveciennes <perisco@louveciennes.fr>, centre de loisirs primaire <clp@louveciennes.fr>, Ecole Leclerc - Primaire <0780595Y@ac-versailles.fr>"];

	    private $mailBody = array();
	    private $mailChild = array();
	    private $mailTitle = "";
	    private $nbMails = 0;

	    public function __construct($child, $goNogo, $beginDate, $endDate)
	    {
	       	$this->child = $child;
	    	$this->goNogo = $goNogo;
	    	$this->beginDate = DateTime::createFromFormat("d-m-Y", $beginDate);
	    	if($endDate != null && $endDate != "")
	    	{
	    		$this->endDate = DateTime::createFromFormat("d-m-Y", $endDate);
		    	if($this->beginDate >= $this->endDate)
		    		die("End date must be after begin date");
	    	}

			for($i = 0; $i < count($this->child); $i++)
			{
				if(!isset($this->childNames[$this->child[$i]]))
					die("Unknown child");

				$this->nbMails++;
				$this->mailBody[$i] = self::$mailBodyParts["genericStart"];

				$this->mailChild[$i] = $this->childNames[$this->child[$i]];
				$this->mailTitle[$i] = $this->kinderGarden[$this->child[$i]].$this->mailChild[$i]." Pungeot ".$this->beginDate->format("d/m/Y");
				if($this->endDate != null)
					$this->mailTitle[$i] .= " => ".$this->endDate->format("d/m/Y");
				$this->mailTo[$i] = $this->kinderGardenMails[$this->child[$i]];
				$this->mailBody[$i] .= $this->mailChild[$i]." Pungeot ";

		    	$this->mailBody[$i] .= $this->goNogo == 0 ? self::$mailBodyParts["go"] : self::$mailBodyParts["noGo"];

				if($this->endDate == null)
    				$this->mailBody[$i] .= self::$mailBodyParts["genericKinderGarden"]."le ".$this->beginDate->format("d/m/Y");
			    else
    				$this->mailBody[$i] .= self::$mailBodyParts["genericKinderGarden"]."du ".$this->beginDate->format("d/m/Y")." au ".$this->endDate->format("d/m/Y");

    			$this->mailBody[$i] .= self::$mailBodyParts["genericRegards"];
	    	}
	    }

	    public function printMail()
	    {
            for($i = 0; $i < $this->nbMails; $i++)
	    	{
	    		$url = "to=".urlencode($this->mailTo[$i])."&cc=".urlencode($this->mailCC)."&subject=".urlencode($this->mailTitle[$i])."&body=".urlencode($this->mailBody[$i]);
	    		echo <<<EOT
	    		<div class="media col-md-3 col-xs-3">
                    <figure class="pull-left">
                        <img class="media-object img-circle img-responsive"  src="images/{$this->mailChild[$i]}.png">
                    </figure>
                </div>
                <div class="col-md-6">
                    <h4 class="list-group-item-heading"> {$this->mailTitle[$i]} </h4>
                    <p class="list-group-item-text"> <strong>To : </strong>{$this->mailTo[$i]}</p>
                    <p class="list-group-item-text"> <strong>Cc : </strong>{$this->mailCC}</p>
                    <br>
                    <p class="list-group-item-text"> {$this->mailBody[$i]}</p>
                </div>
                <div class="text-center">
                    <a  type="button" href="mailto:?{$url}" target="_blank" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-envelope"></span> Envoyer </a>
                </div>
EOT;
	    	}
	    }
	}

	if(!isset($_GET["child"]) || !isset($_GET["go-nogo"]) || !isset($_GET["begin-date"]) || $_GET["begin-date"] == "")
		die("Missing parameters");

	$mail = new Email($_GET["child"], $_GET["go-nogo"], $_GET["begin-date"], isset($_GET["end-date"]) ? $_GET["end-date"] : null);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Notification centre de loisirs</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript" src="/external_libraries/jquery-3.2.0.js"></script>
	<script type="text/javascript" src="/external_libraries/bootstrap-3.3.7-dist/js/bootstrap.js"></script>
	<link rel="stylesheet" type="text/css" href="/external_libraries/bootstrap-3.3.7-dist/css/bootstrap.css">
</head>
	<body>
		<header class="container-fluid">

			<h1><a href="index.html" type="button" class="btn btn-lg"><span class="glyphicon glyphicon-home"></span></a>Envoi des mails</h1>
		</header>

		<div class="list-group">

<?php
	$mail->printMail();
?>
		</div>
	</body>
</html>

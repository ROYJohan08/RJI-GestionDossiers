<!DOCKTYPE html>
<!--
CREATE TABLE `dossier` (
  `dossier` text NOT NULL,
  `creation` text NOT NULL,
  `relance` text NOT NULL,
  `info` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `dossier` (`dossier`, `creation`, `relance`, `info`) VALUES
('065918484', '27/03/2024', '27/03/2024', '123'),
('123456', '27/03/2024', '27/03/2024', '123'),
('456789', '27/03/2024', '27/03/2024', '456'),
('125454', '27/03/2024', '27/03/2024', '1234');
ALTER TABLE `dossier`
  ADD UNIQUE KEY `dossier` (`dossier`) USING HASH;
COMMIT;
-->
<html lang="FR-fr">
	<head>
		<?php
			if(!isset($_SESSION['start'])){session_start();$_SESSION['start']=date("d/m/Y");}
			if(isset($_POST['ajouter']) && $_POST['ajouter']=="Ajouter"){
				if(
					isset($_POST['dossier']) && 
					is_numeric(str_replace("-","",$_POST['dossier'])) && 
					(
						strlen(str_replace(" ","",$_POST['dossier']))==6 || 
						strlen(str_replace("-","",$_POST['dossier']))==9 || 
						strlen(str_replace("-","",$_POST['dossier']))==10 || 
						strlen(str_replace("-","",$_POST['dossier']))==8
					)
				){
					$_POST['dossier']  =str_replace(" ","",str_replace("-","",$_POST['dossier']));
					if(isset($_POST['relance']) && strtotime($_POST['relance'])<=strtotime("today")){
						if(isset($_POST['info']) && strlen($_POST['info'])>0){
							if(($SQL = sqlConnexion())!=false){
								$STM = $SQL->prepare("INSERT INTO `dossier` (`dossier`,`creation`,`relance`,`info`) VALUES(?,?,?,?)");
								$STM->execute(array(clearVariable($_POST['dossier']),date("d/m/Y"),date_format(date_create($_POST['relance']),"d/m/Y"),clearVariable($_POST['info'])));
								$STM = $SQL->prepare("SELECT `dossier` FROM  `dossier` WHERE `dossier`=? ORDER BY `dossier` DESC LIMIT 0,1");
								$STM->execute(array(clearVariable($_POST['dossier'])));
								$RES = $STM->fetchAll();
								$_SESSION['error']=null;
								if(!isset($RES)){$_SESSION['error']="Dossier creaation failed";}
							}else{$_SESSION['error']="sqlConnexion failed";}
						
						}else{$_SESSION['error']="info";}
					}else{$_SESSION['error']="relance";}
				}else{$_SESSION['error']="dossier";}
			}
			elseif(isset($_POST['modifier']) && $_POST['modifier']=="Modifier"){
				if(
					isset($_POST['dossier']) && 
					is_numeric(str_replace("-","",$_POST['dossier'])) && 
					(
						strlen(str_replace(" ","",$_POST['dossier']))==6 || 
						strlen(str_replace("-","",$_POST['dossier']))==9 || 
						strlen(str_replace("-","",$_POST['dossier']))==10 || 
						strlen(str_replace("-","",$_POST['dossier']))==8
					)
				){
					$_POST['dossier']  =str_replace(" ","",str_replace("-","",$_POST['dossier']));
					if(isset($_POST['relance']) && strtotime($_POST['relance'])<=strtotime("today")){
						if(isset($_POST['info']) && strlen($_POST['info'])>0){
							if(($SQL = sqlConnexion())!=false){
								$STM = $SQL->prepare("UPDATE `dossier` SET `creation`=?, `relance`=?, `info`=? WHERE `dossier`=?");
								$STM->execute(array(date("d/m/Y"),date_format(date_create($_POST['relance']),"d/m/Y"),clearVariable($_POST['info']),clearVariable($_POST['dossier'])));
								$_SESSION['error']=null;
							}else{$_SESSION['error']="sqlConnexion failed";}
						
						}else{$_SESSION['error']="info";}
					}else{$_SESSION['error']="relance";}
				}else{$_SESSION['error']="dossier";}
			}
			elseif(isset($_GET['rm'])){
				if(
					isset($_GET['rm']) && 
					is_numeric(str_replace("-","",$_GET['rm'])) && 
					(
						strlen(str_replace(" ","",$_GET['rm']))==6 || 
						strlen(str_replace("-","",$_GET['rm']))==9 || 
						strlen(str_replace("-","",$_GET['rm']))==10 || 
						strlen(str_replace("-","",$_GET['rm']))==8
					)
				){
					$_GET['rm']  =str_replace(" ","",str_replace("-","",$_GET['rm']));
					if(($SQL = sqlConnexion())!=false){
						$STM = $SQL->prepare("DELETE from `dossier`  WHERE `dossier`=?");
						$STM->execute(array(clearVariable($_GET['rm'])));
						$_SESSION['error']=null;
					}else{$_SESSION['error']="sqlConnexion failed";}
				}
			}
			
			
			
			function displayErrorrs(){
				if(isset($_SESSION['error'])){
					if($_SESSION['error']=="dossier" || $_SESSION['error']=="relance" || $_SESSION['error']=="info"){
						echo "<style type='text/css'>#".$_SESSION['error']."{border: solid 8px #FF2020;}</style>";
					}
					else{
						echo "<script type='text/javascript'>console.log('".clearVariable($_SESSION['error'])."');</script>";
					}
				}
			}
			
			function clearVariable($Variable){
				$Return = false;
				try{
					while(strchr($Variable,"/*")){$Variable = str_replace("/*","",$Variable);} //Retrait des commentaires PHP.
					while(strchr($Variable,"*/")){$Variable = str_replace("*/","",$Variable);} //Retrait des commentaires PHP.
					while(strchr($Variable,"\\")){$Variable = str_replace("\\","",$Variable);} //Retrait des commentaires PHP.
					while(strchr($Variable,"//")){$Variable = str_replace("//","",$Variable);} //Retrait des commentaires PHP.
					while(strchr($Variable,"-- -")){$Variable = str_replace("-- -","",$Variable);} //Retrait des commentaires MYSQL.
					while(strchr($Variable,"<!--")){$Variable = str_replace("<!--","",$Variable);} //Retrait des commentaires HTML.
					while(strchr($Variable,"-->")){$Variable = str_replace("-->","",$Variable);} //Retrait des commentaires HTML.
					while(strchr($Variable,"../")){$Variable = str_replace("../","",$Variable);} //Retrait des file inclusion.
					while(strchr($Variable,"*")){$Variable = str_replace("*","",$Variable);} //Retrait des failles SQL *.
					while(strchr($Variable,"=1")){$Variable = str_replace("=1","",$Variable);} //Retrait des failles SQL =1.
					while(strchr($Variable,"union")){$Variable = str_replace("union","",$Variable);} //Retrait des failles SQL union.
					while(strchr($Variable,"UNION")){$Variable = str_replace("UNION","",$Variable);} //Retrait des failles SQL union.
					while(strchr($Variable,"drop")){$Variable = str_replace("drop","",$Variable);} //Retrait des failles SQL drop.
					while(strchr($Variable,"DROP")){$Variable = str_replace("DROP","",$Variable);} //Retrait des failles SQL drop.
					while(strchr($Variable,"deocde")){$Variable = str_replace("decode","",$Variable);} //Retrait des failles XSS des b64 decode.
					while(strchr($Variable,"DEOCDE")){$Variable = str_replace("DECODE","",$Variable);} //Retrait des failles XSS des b64 decode.
					while(strchr($Variable,"eval(")){$Variable = str_replace("eval(","",$Variable);} //Retrait des failles XSS des eval.
					while(strchr($Variable,"EVAL(")){$Variable = str_replace("EVAL(","",$Variable);} //Retrait des failles XSS des eval.
					if(strchr($Variable,"\"")){$Variable = str_replace("\"","\\\"",$Variable);} //Retrait des failles XSS ".
					if(strchr($Variable,"'")){$Variable = str_replace("'","\'",$Variable);} //Retrait des failles XSS '.
					while(substr($Variable,0,1)==" "){$Variable = substr($Variable,1);} //Retrait des espaces doubles ou +.
					while(substr($Variable,strlen($Variable)-1,1)==" "){$Variable = substr($Variable,0,strlen($Variable)-1);} //Retrait des espaces en fin de chaine.
					while(substr($Variable,0,1)==" "){$Variable = substr($Variable,1);} //Retrait des espaces en debut de chaine.
					$Variable = htmlspecialchars($Variable); //Retrait des failles XSS HTML.
					$Return = $Variable;
				}
				catch (Exception $e){
					debug("Erreur sur clearVariable(String):String : ".$e->getMessage()); //Affiche un message en cas d'erreur.
				}
				return $Return;
			}
	
			function sqlConnexion(){
				$Return = false;
				try{
					$Return = new PDO("mysql:host=localhost;dbname=rji;charset=utf8", "root", "");
				}
				catch (Exception $e){
					$_SESSION['error']="Erreur sur sqlConnexion(Void):Object|Boolean : ".$e->getMessage();
				}
				return $Return;
			}
	
	
			displayErrorrs();
		?>
		<script src="https://kit.fontawesome.com/71aa67b3db.js" crossorigin="anonymous"></script>
		<title>Gestion de dossiers</title>
		<style>
			@import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
			html,body{
					background: linear-gradient(180deg, #0178CA, #02985B);
			}
			header{
				width: 100%;
				height: 10%;
				text-align: center;
				padding: 1% 0 2% 0;
				margin: 0 0 5% 0;
				border-bottom: solid 4px #FFFFFF;
			}
			header>h1{
				font-size: 50px;
				color: #FFFFFF;
				font-weight: bold;
				font-family: "Roboto", sans-serif;
			}
			section{
				width: 100%;
				text-align: center;
			}
			aside{
				width: 35%;
				padding: 1% 5% 1% 5%;
				margin: 2%;
				background: rgba(255,255,255,.7);
				display: inline-block;
				vertical-align: top;
			}
			aside h2{
				color: #2B7B7C;
				margin: 0 0 7% 0;
				padding: 0;
				font-size: 35px;
				font-family: "Roboto", sans-serif;
			}
			label{
				width: 100%;
				display: block;
				text-align: left;
				margin-top: 2%;
				padding-left: 10px;
			}
			input,textarea,button{
				width: 100%;
				background-color: #016E77;
				padding:2%;
				font-size: 18px;
				border: none;
				border-radius: 10px;
				font-weight: bold;
				font-family: "Roboto", sans-serif;
			}
			input::placeholder,textarea::placeholder{
				color: #FFFFFF;
				font-family: "Roboto", sans-serif;
				font-weight: 0;
			}
			input[type="submit"],button{
				margin-top: 2%;
				background-color: #F38410;
				text-transform: uppercase;
				color: #FFFFFF;
				padding:1.5% 2% 1.5% 2%;
				font-size: 25px;
			}
			table{
				border-radius: 10px;
			}
			tr:first-child{
				background: rgba(243,123,16,1);
				text-transform: uppercase;
				color: #FFFFFF;
				font-size: 17px;
				border-radius: 10px;
			}
			tr:first-child th{
				padding:1.5% 2% 1.5% 2%;
			}
			tr:first-child>th:first-child{
				border-radius: 10px 0 0 0;
			}
			tr:first-child>th:last-child{
				border-radius: 0 10px  0 0;
			}
			tr:last-child>td:first-child{
				border-radius: 0 0 0 10px;
			}
			tr:last-child>td:last-child{
				border-radius: 0 0 10px 0;
			}
			tr:nth-child(2n){
				background:rgba(0,0,0,.2);
			}
			tr td{
				font-size: 18px;
				padding: .3% 2px .3% 2px;
			}
			tr td:first-child{
				padding-left: 10px;
			}
			td a{
				text-decoraion: none;
				display: inline-block;
				color: #000000;
				font-size: 25px;
				margin: 4%;
				padding: 2%;
			}
			td a:hover{
				color: #FF2020;
			}
			@media (max-width: 1536px) and (min-width: 1308px) {
				tr:first-child{
					font-size: 12px;
				}
				table input,table textarea,table button{
					font-size: 15px;
				}
				table  input[type="submit"],table button{
					font-size: 17px;
				}
			}
			@media (max-width: 1308px) {
				aside{
					display :block;
					margin: 2% 5% 0 5%;
					width: 80%;
				}
				section{text-lign: center;padding: 0;margin: 0;}
			}
		</style>
		<link href="data:image/png;base64,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" rel="icon" type="image/x-icon">
		<script>
			function modify(dossier){
				relance = document.getElementById(dossier).getElementsByClassName('relance')[0].innerHTML;
				rel = relance.split('/');
				relance = rel[2]+"-"+rel[1]+"-"+rel[0];
				creation = document.getElementById(dossier).getElementsByClassName('creation')[0].innerHTML;
				cre = creation.split('/');
				creation = cre[2]+"-"+cre[1]+"-"+cre[0];
				info = document.getElementById(dossier).getElementsByClassName('info')[0].innerHTML;
				document.getElementById(dossier).innerHTML="<td><input type=\"text\" name=\"dossier\" id=\"dossier\"  readonly value=\""+dossier+"\" /></td><td><input type=\"date\" name=\"creation\" id=\"creation\" value=\""+creation+"\" /></td><td><input type=\"date\" name=\"relance\" id=\"relance\" require value=\""+relance+"\" /></td><td><textarea type=\"text\" value=\"\" id=\"info\" name=\"info\" required>"+info+"</textarea></td><td><input type=\"submit\" name=\"modifier\" id=\"modifier\" value=\"Modifier\" /><button onClick=\"annuler("+dossier+");\">Annuler</button></td>";
			}
			function annuler(dossier){
				relance = document.getElementById(dossier).getElementById('relance').value;
				rel = relance.split('-');
				relance = rel[2]+"/"+rel[1]+"/"+rel[0];
				creation = document.getElementById(dossier).getElementById('creation')[0].value;
				cre = creation.split('-');
				creation = cre[2]+"/"+cre[1]+"/"+cre[0];
				info = document.getElementById(dossier).getElementsByClassName('info')[0].innerHTML;
				document.getElementById(dossier).innerHTML="<td class='dossier'>"+dossier+"</td><td class='creation'>"+creation+"</td><td class='relance'>"+relance+"</td><td class='info'>"+info+"</td><td class='info'><a href='?rm="+dossier+"'>X</a><a href='#' onClick=\"modify('"+dossier+"');\">M</a></td>";
			}
		</script>
	</head>
	<body onLoad="document.getElementById('dossier').focus();">
		<header>
			<h1>Gestion des dossiers</h1>
		</header>
		<section>
			<aside>
				<h2>Création d'un dossier</h2>
				<form action="" method="POST">
					<label for="dossier">Numéro de dossier : </label>
					<input type="text" name="dossier" id="dossier"  maxlength="10" required placeholder="Numéro de dossier" />
					<label for="relance">Date de relance : </label>
					<input type="date" name="relance" id="relance"  required value="<?php echo date("Y-m-d");?>" />
					<label for="info">Informations : </label>
					<textarea type="text" value="" id="info" name="info" required>.</textarea>
					<input type="submit" name="ajouter" id="ajouter" value="Ajouter" />
				</form>
			</aside>
			<aside>
				<h2>Liste des dossiers</h2>
				<form method="POST">
					<table width="100%" border="1">
						<tr>
							<th>Dossier</th>
							<th>Création</th>
							<th>Relance</th>
							<th>Informations</th>
							<th></th>
						</tr>
						<?php
							if(($SQL = sqlConnexion())!=false){
									$STM = $SQL->prepare("SELECT * FROM `dossier` ORDER BY `dossier` ASC");
									$STM->execute();
									$RES = $STM->fetchAll();
									foreach($RES as $ROW){
										echo "<tr id='".$ROW['dossier']."' ";
										$T = explode("/",$ROW['relance']);
										if(strtotime($T[2]."-".$T[1]."-".$T[0])<=strtotime("-1 day")){
											echo " style=\"background-color: rgba(255,0,0,.5);\" ";
										}
										echo ">
										<td class='dossier'>";
										if(strlen($ROW['dossier'])>6){
											echo substr($ROW['dossier'],0,4)."-".substr($ROW['dossier'],4);
										}
										else{
											echo $ROW['dossier'];
										}
										echo "</td>
										<td class='creation'>".$ROW['creation']."</td>
										<td class='relance'>".$ROW['relance']."</td>
										<td class='info'>".$ROW['info']."</td>
										<td class='info'><a href='?rm=".$ROW['dossier']."'><i class=\"fa-solid fa-trash\"></i></a><a href='#' onClick=\"modify('".$ROW['dossier']."');\"><i class=\"fa-solid fa-pen-to-square\"></i></a></td>
										</tr>";
									}
								}else{$_SESSION['error']="sqlConnexion failed";}
						?>
					</table>
				</form>
			</aside>
		</section>
	</body>
</html>
<?php
/*----------------------------------------------------COMMENT---------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

// Fonction qui affiche tous les commentaires d'un évènement
// Appel : echo load_comments(variable à récupérer de la page $event_id);
function load_comments($event_id)
{
    $query = "SELECT user_id, content, comment_date, comment_hour FROM commentary WHERE event_id = '$event_id' ORDER BY commentary_id DESC;";
    $resultat = pg_query($query);
    $comments = '';
    while($row = pg_fetch_row($resultat))
    {
        $query_user = '';
        $query_user .= "SELECT user_name, user_forename, user_gender, user_age FROM public.user WHERE user_id = '$row[0]'";
        $usernames = pg_query($query_user);
        $username = pg_fetch_row($usernames);

        $comments .= "<h3>$username[1] $username[0], $username[3] ans ($username[2]), le $row[2] à $row[3]</h3>";
        $comments .= "<p class='commentaire'> $row[1] <p>";
    }
    return $comments;
}

// Fonction qui affiche le formulaire pour commenter un évenement
// Appel : echo comment_form();
function comment_form()
{
    return "<form action='index.php' method='post'><textarea value = '' name='Commentaire' placeholder='Ex : C\'était vraiment génial, J'y serai à 100%...' maxlength='256' rows='5' cols='50'></textarea><input type='submit' value='Commenter' name='BtnCommentaire'></form>";
}

// Fonction qui prend en paramètre la chaine de caractere du commentaire, l'id de l'evenement et l'id de l'utilisateur
/* Appel :  if(isset($_POST['BtnCommentaire']) && $_POST['Commentaire'] != '')
			{
				create_comment($_POST['Commentaire'], variable à récuperer $event_id, variable à récupérer des infos de connection $user_id);
				header("Refresh:0.1");
			}	
*/
function create_comment($comment, $event_id, $user_id)
{
    date_default_timezone_set('Europe/Paris');
    $comment_id = 0;
    $comment_date = ''.date("d").'/'.date("m").'/'.date("Y");
    $comment_hour = ''.date("H").'h'.date("i");
    $query_comment_id = 'SELECT commentary_id FROM commentary';
    $comment_ids = pg_query($query_comment_id);
    while($row = pg_fetch_row($comment_ids))
    {
        $comment_id = $row[0];
    }
    $comment_id += 1;
    $query = "INSERT INTO commentary VALUES ('$comment_id', '$comment', '$event_id', '$user_id', '$comment_date', '$comment_hour');";
    pg_query($query);
}

/*-----------------------------------------------------USER-----------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage du formulaire de choix d'inscription/connexion pour compte utilisateur et organisateur
function accountChoiceForm() {
    $return = null;
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    if($_GET['action'] == 'signIn') {
        $return .= '<td> <h3> Créer un compte </h3>';
        $return .= '</tr> <tr>';
        $return .= '<td> Sélectionner le profil à créer : </td>';
        $return .= '</tr> <tr>';
    }
    else if($_GET['action'] == 'logIn') {
        $return .= '<td> <h3> Se connecter </h3>';
        $return .= '</tr> <tr>';
        $return .= '<td> Sélectionner le profil auquel se connecter : </td>';
        $return .= '</tr> <tr>';
    }
    $return .= '<td> <input type="radio" name="account_choice" value="1"> Utilisateur </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="radio" name="account_choice" value="2"> Organisateur </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> <input type="submit" name="account_next" value="Suivant">';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//Fonction de redirection en fonction de l'action inscription/connexion et du profil séléctionné utilisateur et organisateur
function accountChoice() {
    if(isset($_POST['account_next'])) {
        if(isset($_POST['account_choice'])) {
            $account_choice = $_POST['account_choice'];
            if($_GET['action'] == 'signIn') {
                if($account_choice == 1)
                header('location: account_sign_in.php?account=1');
                if($account_choice == 2)
                header('location: account_sign_in.php?account=2');
            }
            else if($_GET['action'] == 'logIn') {
                if($account_choice == 1)
                header('location: account_log_in.php?account=1');
                if($account_choice == 2)
                header('location: account_log_in.php?account=2');
            }
        } else {
            return '<p style="color: red"> Veuillez séléctionner le type de compte à créer svp </p>';
        }
    }
}

/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage d'un formulaire de création de compte pour utilisateur et organisateur
function createAccountForm() {
    $account_role = $_GET['account'];
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    if($account_role == 1) {
        $return .= '<td colspan=2> <h3> Créer un compte utilisateur : </h3>';
        $return .= '</tr> <tr>';
        $return .= '<td> Nom : </td> <td> <input type="text" name="user_name"> </td>';
        $return .= '</tr> <tr>';
        $return .= '<td> Prénom </td> <td> <input type="text" name="user_forename"> </td>';
        $return .= '</tr> <tr>';
        $return .= '<td> Age </td> <td> <input type="text" name="user_age"> </td>';
        $return .= '</tr> <tr>';
        $return .= '<td> Sexe </td>';
        $return .= '<td> <select name="user_gender">';
        $return .= '<option value="" disabled selected hidden> Sélectionner un sexe </option>';
        $return .= '<option value="Homme"> Homme </option>';
        $return .= '<option value="Femme"> Femme </option>';
        $return .= '</select> </td>';
        $return .= '</tr> <tr>';
    }
    else if($account_role == 2) {
        $return .= '<td colspan=2> <h3> Créer un compte association : </h3>';
        $return .= '</tr> <tr>';
        $return .= '<td> Nom de l\'association : </td> <td> <input type="text" name="org_name"> </td>';
        $return .= '</tr> <tr>';
        $return .= '<td> Description de l\'association : </td> <td> <input type="text" name="org_description"> </td>';
        $return .= '</tr> <tr>';
    }
    $return .= '<td> Identifiant </td> <td> <input type="text" name="login"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe </td> <td> <input type="password" name="pass1"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe </td> <td> <input type="password" name="pass2"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2> <input type="submit" name="create_account" value="S\'inscrire"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//Fonction de création de compte pour utilisateur et organisateur
function createAccount() {
    $account_role = $_GET['account'];
    $return = null;
    if(isset($_POST['create_account'])) {
        //Création d'un compte utilisateur
        if($account_role == 1) {
            $user_name = $_POST['user_name'];
            $user_forename = $_POST['user_forename'];
            $user_age =  $_POST['user_age'];
            $user_log = $_POST['login'];
            $user_pass1 = $_POST['pass1'];
            $user_pass2 = $_POST['pass2'];

            if(isset($_POST['user_gender'])) {
                $user_gender = $_POST['user_gender'];
                if($user_name != "" AND $user_forename !="" AND $user_log != "" AND $user_pass1 != "" AND $user_pass2 != "" AND $user_gender != "" AND $user_age != "") {
                    if($user_pass1 === $user_pass2) {
                        if(is_numeric($user_age)) {
                            $query = "INSERT INTO public.user(user_name, user_forename, user_gender, user_age, user_log, user_pass, user_role) VALUES ('$user_name', '$user_forename', '$user_gender', '$user_age', '$user_log', '$user_pass1', '$account_role')";
                            $res = pg_query($query);
                            if($res) {
                                $return = "Inscription réussie.";
                            } else {
                                $return = pg_last_error();
                            }
                        } else {
                            $return = 'Le champs "âge" est invalide.';
                        }
                    } else {
                        $return = 'Les mots de passe ne correspondent pas.';
                    }
                } else {
                    $return = 'Veuillez remplir les champs requis.';
                }
            } else {
                $return = 'Veuillez remplir les champs requis.';
            }
        }
        //Création d'un compte organisateur
        if($account_role == 2) {
            $org_name = $_POST['org_name'];
            $org_description = $_POST['org_description'];
            $org_log = $_POST['login'];
            $org_pass1 = $_POST['pass1'];
            $org_pass2 = $_POST['pass2'];
            if($org_name != "" AND $org_log != "" AND $org_pass1 != "" AND $org_pass2 != "") {
                if($org_pass1 === $org_pass2) {
                    $query = "INSERT INTO public.organizer(org_name, org_description, org_log, org_pass) VALUES('$org_name', '$org_description', '$org_log', '$org_pass1')";
                    $res = pg_query($query);
                    if($res) {
                        $return = "Inscription réussie.";
                    } else {
                        $return = pg_last_error();
                    }
                } else {
                    $return = 'Les mots de passe ne correspondent pas.';
                }
            } else {
                $return = 'Veuillez remplir les champs requis';
            }
        }
        if($account_role == 0) {
            //CREATION D'UN ADMIN
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage d'un formulaire de connexion pour utilisateur et organisateur
function connectAccountForm() {
    $user_role = $_GET['account'];
    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> Connectez-vous : </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Identifiant : </td>';
    $return .= '<td> <input type="text" name="login"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Mot de passe : </td>';
    $return .= '<td> <input type="password" name="pass"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2> <input type="submit" name="account_log_in" value="Se connecter"> </td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//Fonction de connexion pour utilisateur OU organisateur
function connectAccount() {
    $account_role = $_GET['account'];
    $return = null;
    if(isset($_POST['account_log_in'])) {
        if($account_role == 0 || $account_role == 1) {
            $user_log = $_POST['login'];
            $user_pass = $_POST['pass'];
            if($user_log != "" AND $user_pass != "") {
                $query = "SELECT * FROM public.user WHERE user_log = '$user_log' AND user_pass = '$user_pass'";
                $results = pg_query($query);
                if(pg_num_rows($results) > 0) {
                    session_start();
                    $rows = pg_fetch_row($results);
                    $_SESSION['account_connected'] = true;
                    $_SESSION['account_id'] = $rows[0];
                    $_SESSION['account_name'] = $rows[1];
                    $_SESSION['account_forename'] = $rows[2];
                    $_SESSION['account_gender'] = $rows[3];
                    $_SESSION['account_age'] = $rows[4];
                    $_SESSION['account_role'] = $rows[5];
                    $_SESSION['account_log'] = $rows[6];
                    $return = 'Connexion réussie.';
                    header('location: ../index.php');
                } else {
                    $return = 'Mot de passe ou identifiant incorrect.';
                }
            } else {
                $return = 'Champs requis.';
            }
        }
        if($account_role == 2) {
            $org_log = $_POST['login'];
            $org_pass = $_POST['pass'];
            if($org_log != "" AND $org_pass != "") {
                $query = "SELECT * FROM public.organizer WHERE org_log = '$org_log' AND org_pass = '$org_pass'";
                $results = pg_query($query);
                if(pg_num_rows($results) > 0) {
                    session_start();
                    $rows = pg_fetch_row($results);
                    $_SESSION['account_connected'] = true;
                    $_SESSION['account_id'] = $rows[0];
                    $_SESSION['account_name'] = $rows[1];
                    $_SESSION['account_role'] = 2;
                    $_SESSION['account_log'] = $rows[3];
                    $return = 'Connexion réussie.';
                    header('location: ../index.php');
                } else {
                    $return = 'Mot de passe ou identifiant incorrect.';
                }
            } else {
                $return = 'Champs requis.';
            }
        }
    }
    $return = '<p style="color: red;">' .$return .'</p>';
    return $return;
}

/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage des informations de connexion pour utilisateur et organisateur
function sessionInformation() {
    $return = '<table>';
    $return .= '<tr>';
    if(!isset($_SESSION['account_connected'])) {
        $return .= '<td> Vous n\'êtes pas connecté. </td>';
    } else if(isset($_SESSION['account_connected']) && $_SESSION['account_connected'] == true) {
        $return .= '<td colspan=2> <h3> Connecté en tant que </h3> </td>';
        $return .= '</tr> <tr>';
        if($_SESSION['account_role'] != 2) {
            $return .= '<td> Nom : ' .$_SESSION['account_name'] .'</td> <td> Prénom : ' .$_SESSION['account_forename'] .'</td>';
            $return .= '</tr> <tr>';
            $return .= '<td colspan=2> <h3> Informations de sessions </h3> </td>';
            $return .= '</tr> <tr>';
            $return .= '<td> Sexe : ' .$_SESSION['account_gender'] .' </td> <td> Age : ' .$_SESSION['account_age'] .'</td>';
        }
        else if($_SESSION['account_role'] == 2) {
            $return .= '<td colspan=2sssssssssssssssssss> Nom : ' .$_SESSION['account_name'] .'</td>';
        }
        switch($_SESSION['account_role']) {
            case 0 :
            $account_role = 'Administrateur';
            break;
            case 1 :
            $account_role = 'Utilisateur';
            break;
            case 2 :
            $account_role = 'Organisateur';
            break;
            default:
            $account_role = 'Utilisateur';
            break;
        }
        $return .= '</tr> <tr>';
        $return .= '<td> Profil : ' .$account_role .' </td> <td> Login : ' .$_SESSION['account_log'] .'</td>';
        $return .= '</tr> <tr>';
        $return .= '<form action="pages/account_log_out.php" method="POST">';
        $return .= '<td colspan=2> <input type="submit" name="account_log_out" value="Se déconnecter">';
        $return .= '</form> </tr>';
    }
    $return .= '</table>';
    return $return;
}

/*----------------------------------------------------EVENT-----------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage d'un formulaire pour la création d'évènements
function createEventForm() {
    $return = '<form action="#" method="post">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=4> <h3> Créer un événement </h3> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left;"> Nom de l\'événement :</td> <td colspan=2> <input type="text" style="width: 400px;" name="nomEvent"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left;"> Description de l\'événement :</td> <td colspan=2> <textarea name="descriptionEvent" style="height: 80px; width: 400px;"> </textarea> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left;"> Catégorie :</td>';
    $return .= '<td colspan=2> <select name=categorie style="width: 400px;">';
    $return .= '<option value="" disabled selected hidden> Sélectionner une catégorie </option>';
    $return .= '<option value="Art"> Art </option>';
    $return .= '<option value="Causes"> Causes </option>';
    $return .= '<option value="Comédie"> Comédie </option>';
    $return .= '<option value="Artisanat"> Artisanat </option>';
    $return .= '<option value="Danse"> Danse </option>';
    $return .= '<option value="Boissons"> Boissons </option>';
    $return .= '<option value="Film"> Film </option>';
    $return .= '<option value="Fitness"> Fitness </option>';
    $return .= '<option value="Alimentation"> Alimentation </option>';
    $return .= '<option value="Jeux"> Jeux </option>';
    $return .= '<option value="Jardinage"> Jardinage </option>';
    $return .= '<option value="Sante"> Sante </option>';
    $return .= '<option value="Maison"> Maison </option>';
    $return .= '<option value="Littérature"> Littérature </option>';
    $return .= '<option value="Musique"> Musique </option>';
    $return .= '<option value="Fête"> Fête </option>';
    $return .= '<option value="Religion"> Religion </option>';
    $return .= '<option value="Shopping"> Shopping </option>';
    $return .= '<option value="Sport"> Sport </option>';
    $return .= '<option value="Théâtre"> Théâtre </option>';
    $return .= '<option value="Bien-être"> Bien-être </option>';
    $return .= '<option value="Autre"> Autre </option>';
    $return .= '</select> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td style="text-align: left"> Date :</td> <td style="text-align: left; width: 150px;"> <input type="date" name="dateEvent"> </td>';
    $return .= '<td style="text-align: left"> Heure :</td> <td style="text-align: left;">';
    $return .= '<select name="heure">';
    for ($i=0; $i < 24; $i++) {
        if($i<10)
        $return .= '<option value="0'. $i .'"> 0'. $i .' </option>';
        else {
            $return .= '<option value="'. $i .'"> '. $i .' </option>';
        }
    }
    $return .= '</select> : <select name=minute>';
    $return .= '<option value="00"> 00 </option>';
    $return .= '<option value="15"> 15 </option>';
    $return .= '<option value="30"> 30 </option>';
    $return .= '<option value="45"> 45 </option>';
    $return .= '</select> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left"> Adresse :</td> <td colspan=2> <input type="text" style="width: 400px;" name="adresseEvent"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left"> Complément :</td> <td colspan=2> <input type="text" style="width: 400px;" name="complementAdresseEvent"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td style="text-align: left;"> Code postal : </td> <td style="text-align: left;"> <input type="text" style="width: 150px;" name="codePostal"> </td>';
    $return .= '<td style="text-align: left"> Ville : </td> <td style="text-align: right"> <input type="text" style="width: 290px;" name="villeEvent"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=4> <input type="submit" style="width: 80px;" name="submit" value="Créer">';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//Fonction de création d'évènements
function createEvent() {
    if(isset($_POST['submit'])){
        $nomEvent = $_POST['nomEvent'];
        $description = $_POST['descriptionEvent'];
        $categorie = $_POST['categorie'];
        $date = $_POST['dateEvent'];
        $heure = $_POST['heure'];
        $minute = $_POST['minute'];
        $adresse = $_POST['adresseEvent'];
        $complement = $_POST['complementAdresseEvent'];
        $codePostal = $_POST['codePostal'];
        $ville = $_POST['villeEvent'];

        $return = null;

        if ($nomEvent != "" AND $description != "" AND $categorie != "" AND isset($date) AND $adresse != "" AND $codePostal != NULL AND $ville != "") {
            $query = "INSERT INTO event(name, description, type, event_date, hour, minute, nb_Interested, nb_Participation, nb_People, finished, organizer_id) VALUES ('$nomEvent', '$description', '$categorie', '$date', '$heure', '$minute', 0, 0, 0,false, 1)";
            $res = pg_query($query);
            if ($res) {
                $return = "Evenement correctement créé.";
            } else {
                $return = "<p style='color:red;'> Error: " . $query . "<br>" . pg_last_error() .".</p>";
            }
        } else {
            $return = '<p style="color:red;"> Veuillez remplir tous les champs. </p>';
        }
//<<<<<<< HEAD

        return $return;
    }
//=======
    //}
    return $return;
//>>>>>>> ed13a1e08504cbd4aac4d215d3428456a6fe5288
}

/*--------------------------------------------------------------------------------------------------------------*/

//Fonction d'affichage des évèenements à venir
function list_event() {
    $query = 'SELECT name,type,event_date,org_name,event_id FROM event INNER JOIN organizer ON organizer_id = org_id WHERE finished = false';
    $results = pg_query($query);

    $return = '<form action="#" method="POST">';
    $return .= '<table>';
    $return .= '<tr> <td colspan = 5> Evènements à venir </td> </tr>';
    $return .= '<tr> <th> Nom </th> <th> Type </th> <th> Date </th> <th> Organisateur </th> <th> En savoir plus </th> </tr> ';
    $num_res = 0;

    while($rows = pg_fetch_row($results)) {
        $return .= '<tr>';
        $count = count($rows);
        $num_res = $rows[$count-1];

        for($i = 0; $i < $count-1; $i++) {
            $c_row = current($rows);
            $return .= '<td>' . $c_row . '</td>';
            next($rows);
        }
        $return .= '<td> <input type="submit" value="En savoir plus" name="event'.$num_res .'"> </td>';
        $return .= '</tr>';
    }
    $return .= '</table>';
    pg_free_result($results);

    $query = 'SELECT name,type,event_date,org_name,event_id FROM event INNER JOIN organizer ON organizer_id = org_id WHERE finished = true';
    $results = pg_query($query);

    $return .= '<table>';
    $return .= '<tr> <td colspan = 5> Evènements passés </td> </tr>';
    $return .= '<tr> <th> Nom </th> <th> Type </th> <th> Date </th> <th> Organisateur </th> <th> En savoir plus </th> </tr>';
    $num_res = 0;

    while($rows = pg_fetch_row($results)) {
        $return .= '<tr>';
        $count = count($rows);
        $num_res = $rows[$count-1];

        for($i = 0; $i < $count-1; $i++) {
            $c_row = current($rows);
            $return .= '<td>' . $c_row . '</td>';
            next($rows);
        }
        $return .= '<td> <input type="submit" value="En savoir plus" name="event'.$num_res .'"> </td>';
        $return .= '</tr>';
    }
    $return .= '</table>';
    $return .= '<input type="submit" name="back" value="Retour"/>';
    $return .= '</form>';
    pg_free_result($results);
    return $return;
}

//Fonction d'affichage des détails d'un évènements
function details_event() {
    $event_id = $_GET['event_id'];
    $query = 'SELECT * FROM event INNER JOIN organizer ON event.organizer_id = organizer.org_id INNER JOIN location ON event.location_id = location.location_id WHERE event_id = ' .$event_id;
    $results = pg_query($query);
    $rows = pg_fetch_row($results);
    $return = '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=2> <h2>' .$rows[1] .'</h2> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2> <h3> Organisé par ' .$rows[14] .' </h3> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2> Description de l\'évènement : </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2>' .$rows[2] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Catégrorie : </td> <td>' .$rows[3] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Date : </td> <td>' .$rows[8] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Heure : </td> <td>' .$rows[10] .' : ' .$rows[11] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Lieu : </td> <td>' .$rows[22] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2>' .$rows[17] .' ' .$rows[18] .' ' .$rows[19] .' ' .$rows[20] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2>' .$rows[21] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nombre de places : </td>';
    if($rows[5] == NULL)
    $return .= '<td> Accès libre </td>';
    else
    $return .= '<td>' .$rows[5] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nombre de participants : </td> <td>' .$rows[4] .'</td>';
    $return .= '</tr> <tr>';
    $return .= '<td> Nombre d\'intéressés : </td> <td>' .$rows[9] .'</td>';
    $return .= '</tr>';
    $return .= '</table>';
    $return .= '<form action="#" method="POST">';
    $return .= '<input type="submit" name="back" value="Retour"/>';
    $return .= '</form>';

    return $return;
}

//Fonction d'affichage d'un formulaire pour la création d'un lieu
function createPlaceForm() {
    $return = '<form action="#" method="post">';
    $return .= '<table>';
    $return .= '<tr>';
    $return .= '<td colspan=4> <h3> Créer un lieu </h3> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left;"> Nom du lieu :</td> <td colspan=2> <input type="text" style="width: 400px;" name="nomLieu"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 float:left style="text-align: left;"> Numéro de rue :</td> <td colspan=2> <input type="text" style="width: 50px;" name="num"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left;"> Nom de rue :</td> <td colspan=2> <input type="text" style="width: 400px;" name="nomRue"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left;"> Ville :</td> <td colspan=2> <input type="text" style="width: 400px;" name="ville"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left;"> Code Postal :</td> <td colspan=2> <input type="text" style="width: 100px;" name="codePostal"> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=2 style="text-align: left;"> Complément :</td> <td colspan=2> <textarea name="complement" style="height: 80px; width: 400px;"> </textarea> </td>';
    $return .= '</tr> <tr>';
    $return .= '<td colspan=4> <input type="submit" style="width: 80px;" name="submit" value="Créer">';
    $return .= '</table>';
    $return .= '</form>';
    return $return;
}

//Fonction de création de lieu
function createPlace() {
    if(isset($_POST['submit'])){
        $lieuId = $_POST['lieuId'];
        $nomRue = $_POST['nomRue'];
        $num = $_POST['num'];
        $ville = $_POST['ville'];
        $codePostal = $_POST['codePostal'];
        $nomLieu = $_POST['nomLieu'];
        $complement = $_POST['complement'];

        $return = null;

        if ($nomRue != "" AND $num != "" AND $ville != "" AND $codePostal != NULL AND $nomLieu != "") {
            $query = "INSERT INTO location(location_nb, location_street, location_city, location_postalcode, location_complement, location_name) VALUES ('$num', '$nomRue', '$ville', '$codePostal', $complement, $nomLieu)";
            $res = pg_query($query);
            if ($res) {
                $return = "Lieu correctement créé.";
            } else {
                $return = "<p style='color:red;'> Error: " . $query . "<br>" . pg_last_error() .".</p>";
            }
        } else {
            $return = '<p style="color:red;"> Veuillez remplir tous les champs. </p>';
        }
        return $return;
    }
    return $return;
}

/*----------------------------------------------------OTHER-----------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

//Fonction de calul de l'id max parmis les résultats d'une requête
function max_id($query) {
    $max_id = 0;
    $results = pg_query($query);
    if(!$results)
    return NULL;
    else {
        while($rows = pg_fetch_row($results)) {
            $c_row = current($rows);
            if($c_row > $max_id)
            $max_id = $c_row;
        }
        return $max_id;
    }
}

//Fonction d'affichage d'un bouton de retour vers l'index
function displayBackBtn(){
    $return = '<form action="../index.php" method="POST" >';
    $return .= '<input type="submit" name="back_page" value="Retour">';
    $return .= '</form>';
    return $return;
}

/*--------------------------------------------------------------------------------------------------------------*/
/*--------------------------------------------------------------------------------------------------------------*/

?>

<?php

declare(strict_types=1);

namespace Modules\TwoCore\Support;

use Two\Support\Facades\Config;


class TwoError
{

    /**
     * 
     *
     * @param   string  $e_code  [$e_code description]
     *
     * @return
     */
    public static function code(string $e_code): mixed
    {

        if ($e_code == "0001") {
            $error_msg = translate("Pas de connexion à la base forums.");
        }
    
        if ($e_code == "0002") {
            $error_msg = translate("Le forum sélectionné n'existe pas.");
        }
    
        if ($e_code == "0004") {
            $error_msg = translate("Pas de connexion à la base topics.");
        }
    
        if ($e_code == "0005") {
            $error_msg = translate("Erreur lors de la récupération des messages depuis la base.");
        }
    
        if ($e_code == "0006") {
            $error_msg = translate("Entrer votre pseudonyme et votre mot de passe.");
        }
    
        if ($e_code == "0007") {
            $error_msg = translate("Vous n'êtes pas le modérateur de ce forum, vous ne pouvez utiliser cette fonction.");
        }
    
        if ($e_code == "0008") {
            $error_msg = translate("Mot de passe erroné, refaites un essai.");
        }
    
        if ($e_code == "0009") {
            $error_msg = translate("Suppression du message impossible.");
        }
    
        if ($e_code == "0010") {
            $error_msg = translate("Impossible de déplacer le topic dans le Forum, refaites un essai.");
        }
    
        if ($e_code == "0011") {
            $error_msg = translate("Impossible de verrouiller le topic, refaites un essai.");
        }
    
        if ($e_code == "0012") {
            $error_msg = translate("Impossible de déverrouiller le topic, refaites un essai.");
        }
    
        if ($e_code == "0013") {
            $error_msg = translate("Impossible d'interroger la base.") . "<br />Error: sql_error()";
        }
    
        if ($e_code == "0014") {
            $error_msg = translate("Utilisateur ou message inexistant dans la base.");
        }
    
        if ($e_code == "0015") {
            $error_msg = translate("Le moteur de recherche ne trouve pas la base forum.");
        }
    
        if ($e_code == "0016") {
            $error_msg = translate("Cet utilisateur n'existe pas, refaites un essai.");
        }
    
        if ($e_code == "0017") {
            $error_msg = translate("Vous devez obligatoirement saisir un sujet, refaites un essai.");
        }
    
        if ($e_code == "0018") {
            $error_msg = translate("Vous devez choisir un icône pour votre message, refaites un essai.");
        }
    
        if ($e_code == "0019") {
            $error_msg = translate("Message vide interdit, refaites un essai.");
        }
    
        if ($e_code == "0020") {
            $error_msg = translate("Mise à jour de la base impossible, refaites un essai.");
        }
    
        if ($e_code == "0021") {
            $error_msg = translate("Suppression du message sélectionné impossible.");
        }
    
        if ($e_code == "0022") {
            $error_msg = translate("Une erreur est survenue lors de l'interrogation de la base.");
        }
    
        if ($e_code == "0023") {
            $error_msg = translate("Le message sélectionné n'existe pas dans la base forum.");
        }
    
        if ($e_code == "0024") {
            $error_msg = translate("Vous ne pouvez répondre à ce message, vous n'en êtes pas le destinataire.");
        }
        
        if ($e_code == "0025") {
            $error_msg = translate("Vous ne pouvez répondre à ce topic il est verrouillé. Contacter l'administrateur du site.");
        }
    
        if ($e_code == "0026") {
            $error_msg = translate("Le forum ou le topic que vous tentez de publier n'existe pas, refaites un essai.");
        }
    
        if ($e_code == "0027") {
            $error_msg = translate("Vous devez vous identifier.");
        }
    
        if ($e_code == "0028") {
            $error_msg = translate("Mot de passe erroné, refaites un essai.");
        }
    
        if ($e_code == "0029") {
            $error_msg = translate("Mise à jour du compteur des envois impossible.");
        }
    
        if ($e_code == "0030") {
            $error_msg = translate("Le forum dans lequel vous tentez de publier n'existe pas, merci de recommencez");
        }
    
        if ($e_code == "0031") {
            return 0;
        }
    
        if ($e_code == "0035") {
            $error_msg = translate("Vous ne pouvez éditer ce message, vous n'en êtes pas le destinataire.");
        }
    
        if ($e_code == "0036") {
            $error_msg = translate("Vous n'avez pas l'autorisation d'éditer ce message.");
        }
    
        if ($e_code == "0037") {
            $error_msg = translate("Votre mot de passe est erroné ou vous n'avez pas l'autorisation d'éditer ce message, refaites un essai.");
        }
    
        if ($e_code == "0101") {
            $error_msg = translate("Vous ne pouvez répondre à ce message.");
        }
    
        // $header = Config::get('two_core::config.theme.header');

        // if (!isset($header)){
        //     include("themes/default/header.php");
        // }
    
        echo '
        <div class="alert alert-danger"><strong>'. Config::get('two_core::config.sitename') .'<br />'. translate("Erreur du forum") .'</strong><br />';
        echo translate("Code d'erreur :") .' '. $e_code .'<br /><br />';
        echo $error_msg .'<br /><br />';
        echo '<a href="javascript:history.go(-1)" class="btn btn-secondary">'. translate("Retour en arrière") .'</a><br /></div>';
    
        //include("themes/default/footer.php");
    
        //die('');
    }

}